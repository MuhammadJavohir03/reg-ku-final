<?php

namespace App\Http\Controllers;

use App\Models\subject;
use App\Models\grade;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VedomostController extends Controller
{
    /**
     * Shablon faylining joylashuvi.
     * Faylni resources/templates/vedomost_shablon.xlsx ga joylashtiring.
     */
    private function templatePath(): string
    {
        return resource_path('templates/vedomost_shablon.xlsx');
    }

    /**
     * Fanga tegishli talabalar ro'yxatini (grade + user) tayyorlaydi.
     * Tartib: import qilingan tartib bo'yicha (grade.id ga ko'ra), keyin guruh bo'yicha guruhlangan.
     */
    private function getStudents(subject $subject)
    {
        $grades = grade::with('user')
            ->where('subject_id', $subject->id)
            ->orderBy('id')
            ->get();

        $students = $grades->map(function ($g) {
            $user = $g->user;
            return [
                'ismi'    => $user->{"To‘liq_ismi"} ?? $user->Toliq_ismi ?? $user->toliq_ismi ?? '-',
                'guruh'   => $user->Guruh ?? '-',
                'joriy'   => is_numeric($g->joriy_baho) ? $g->joriy_baho : 0,
                'oraliq'  => is_numeric($g->oraliq_baho) ? $g->oraliq_baho : 0,
                'reyting' => is_numeric($g->joriy_oraliq) ? $g->joriy_oraliq : 0,
                'yakuniy' => is_numeric($g->yakuniy_baho) ? $g->yakuniy_baho : 0,
                'umumiy'  => is_numeric($g->umumiy) ? $g->umumiy : 0,
            ];
        });

        // Guruh bo'yicha tartiblab, guruh ichida asl tartibni saqlab qo'yamiz
        $students = $students->sortBy('guruh')->values();

        return $students;
    }

    /**
     * Guruhlar ro'yxati (fan ichida nechta xil guruh bo'lsa, o'shalar).
     */
    private function getGroups($students)
    {
        return $students->pluck('guruh')->unique()->values();
    }

    /**
     * Umumiy baho asosida "Raqamli ekvivalent"ni hisoblaydi.
     */
    private function calcScale($umumiy)
    {
        $u = (float) $umumiy;
        if ($u >= 95) return 4.5;
        if ($u >= 90) return 4;
        if ($u >= 80) return 3.5;
        if ($u >= 70) return 3;
        if ($u >= 65) return 2.5;
        if ($u >= 60) return 2;
        return 0;
    }

    /**
     * Umumiy baho asosida "Harfiy ekvivalent"ni hisoblaydi.
     */
    private function calcLetter($umumiy)
    {
        $u = (float) $umumiy;
        if ($u >= 95) return 'A+';
        if ($u >= 90) return 'A';
        if ($u >= 80) return 'B+';
        if ($u >= 70) return 'B';
        if ($u >= 65) return 'C+';
        if ($u >= 60) return 'C';
        return 'F';
    }

    /**
     * Umumiy baho asosida "An'anaviy baho"ni hisoblaydi.
     */
    private function calcAnan($umumiy)
    {
        $u = (float) $umumiy;
        if ($u >= 70) return 'Yaxshi';
        if ($u >= 60) return 'Qoniqarli';
        return 'Qoniqarsiz';
    }

    /**
     * PREVIEW / TAHRIRLASH SAHIFASI
     * "Vedomostga export" tugmasi bosilganda shu sahifa yangi oynada ochiladi.
     */
    public function form(subject $subject)
    {
        $students = $this->getStudents($subject);
        $groups   = $this->getGroups($students);

        // Sarlavha maydonlari uchun taxminiy standart qiymatlar
        $defaults = [
            'oquv_yili'      => (date('Y') - 1) . '-' . date('Y') . " o'quv yili",
            'kafedra'        => '',
            'talim_tili'     => "O'zbek",
            'imtihon_sanasi' => '________________',
            'semestr'        => $subject->semster ? $subject->semster . '-semestr' : '',
        ];

        return view('subject.vedomost', [
            'subject'  => $subject,
            'students' => $students,
            'groups'   => $groups,
            'defaults' => $defaults,
        ]);
    }

    /**
     * HAQIQIY EXPORT - shablonni to'ldirib, xlsx faylni yuklab beradi.
     */
    public function export(Request $request, subject $subject)
    {
        $data = $request->validate([
            'oquv_yili'      => 'nullable|string|max:255',
            'kafedra'        => 'nullable|string|max:255',
            'talim_tili'     => 'nullable|string|max:255',
            'imtihon_sanasi' => 'nullable|string|max:255',
            'semestr'        => 'nullable|string|max:255',
        ]);

        $students = $this->getStudents($subject);
        $groups   = $this->getGroups($students);

        $spreadsheet = IOFactory::load($this->templatePath());
        $sheet = $spreadsheet->getActiveSheet();

        // --- SARLAVHA QISMI ---
        // A1 "Kokand University" - o'zgarmaydi (shablonda bor)
        $sheet->setCellValue('A2', 'Yakuniy qaydnoma ' . $groups->implode(', '));
        $sheet->setCellValue('A3', $data['oquv_yili'] ?? '');
        $sheet->setCellValue('C4', $data['kafedra'] ?? '');
        $sheet->setCellValue('C5', $subject->nomi);
        $sheet->setCellValue('C6', $data['talim_tili'] ?? '');
        $sheet->setCellValue('C7', $data['imtihon_sanasi'] ?? '________________');
        $sheet->setCellValue('C8', $data['semestr'] ?? '');

        // --- TALABALAR JADVALI ---
        $firstDataRow    = 13;
        $lastTemplateRow = 297; // shablonda tayyor turgan oxirgi qator (285 ta talaba uchun)
        $templateRows    = $lastTemplateRow - $firstDataRow + 1;
        $actualCount     = $students->count();
        $diff            = $actualCount - $templateRows;

        // Shablonning 1-qatoridagi (13-qator) HAR BIR USTUNI formatini alohida-alohida
        // "etalon" sifatida saqlab qo'yamiz (chunki A,B,C,...K ustunlarining formati bir-biridan
        // farq qiladi: A/D-K markazlashtirilgan, B/C oddiy). Keyinroq bu formatni har bir
        // qatorga majburan qo'llaymiz — bu talabalar soni qancha bo'lishidan (10 ta ham,
        // 1000 ta ham) qat'i nazar barcha qatorlar bir xil ko'rinishda chiqishini kafolatlaydi.
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        $colRefStyles = [];
        foreach ($columns as $col) {
            $colRefStyles[$col] = $sheet->getStyle("{$col}{$firstDataRow}")->exportArray();
        }

        if ($diff > 0) {
            // Talabalar shablondagidan ko'p - qo'shimcha qatorlar qo'shamiz
            // (pastdagi bo'sh qatorlar va imzo joyi avtomatik pastga suriladi)
            $sheet->insertNewRowBefore($lastTemplateRow + 1, $diff);
        } elseif ($diff < 0) {
            // Talabalar shablondagidan kam - ortiqcha qatorlarni o'chiramiz
            $toRemove = abs($diff);
            $sheet->removeRow($lastTemplateRow + 1 - $toRemove, $toRemove);
        }

        $row = $firstDataRow;
        foreach ($students as $i => $s) {
            // Har bir qatordagi har bir ustunga o'z etalon formatini majburan qo'llaymiz
            foreach ($columns as $col) {
                $sheet->getStyle("{$col}{$row}")->applyFromArray($colRefStyles[$col]);
            }

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $s['ismi']);
            $sheet->setCellValue("C{$row}", $s['guruh']);
            $sheet->setCellValue("D{$row}", $s['joriy']);
            $sheet->setCellValue("E{$row}", $s['oraliq']);
            $sheet->setCellValue("F{$row}", $s['reyting']);
            $sheet->setCellValue("G{$row}", $s['yakuniy']);
            $sheet->setCellValue("H{$row}", $s['umumiy']);

            // Formula o'rniga to'g'ridan-to'g'ri hisoblangan qiymat yoziladi
            // (formula qoldirilsa, ba'zi dasturlar eski keshlangan qiymatni ko'rsatib qo'yishi mumkin)
            $sheet->setCellValue("I{$row}", $this->calcScale($s['umumiy']));
            $sheet->setCellValue("J{$row}", $this->calcLetter($s['umumiy']));
            $sheet->setCellValue("K{$row}", $this->calcAnan($s['umumiy']));

            $row++;
        }

        // "Talabalar soni:", "Topshirgan:", "Topshirmagan:" yozuvlarini qidirib, avtomatik to'ldiramiz.
        // Topshirgan = umumiy bahosi 0 dan katta bo'lgan talabalar soni
        // Topshirmagan = umumiy bahosi 0 bo'lgan talabalar soni
        $topshirganCount = $students->filter(fn ($s) => (float) $s['umumiy'] > 0)->count();
        $topshirmaganCount = $students->filter(fn ($s) => (float) $s['umumiy'] <= 0)->count();

        foreach ($sheet->getRowIterator() as $r) {
            $label = trim((string) $sheet->getCell('B' . $r->getRowIndex())->getValue());

            if ($label === 'Talabalar soni:') {
                $sheet->setCellValue('C' . $r->getRowIndex(), $actualCount);
            } elseif ($label === 'Topshirgan:') {
                $sheet->setCellValue('C' . $r->getRowIndex(), $topshirganCount);
            } elseif ($label === 'Topshirmagan:') {
                $sheet->setCellValue('C' . $r->getRowIndex(), $topshirmaganCount);
            }
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'Vedomost_' . \Illuminate\Support\Str::slug($subject->nomi) . '_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}