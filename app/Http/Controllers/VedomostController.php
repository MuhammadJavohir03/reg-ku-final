<?php

namespace App\Http\Controllers;

use App\Models\subject;
use App\Models\grade;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use ZipArchive;

class VedomostController extends Controller
{
    /**
     * Fanga tegishli barcha talabalarni guruh bo'yicha guruhlab qaytaradi.
     * Natija: ['GURUH-NOMI' => [ [...student...], ... ], ... ]
     */
    private function getStudentsByGroup(subject $subject): array
    {
        $grades = grade::with('user')
            ->where('subject_id', $subject->id)
            ->orderBy('id')
            ->get();

        $grouped = [];

        foreach ($grades as $g) {
            $user  = $g->user;
            $guruh = $user->Guruh ?? '-';

            $grouped[$guruh][] = [
                'ismi'      => $user->{"To‘liq_ismi"} ?? $user->Toliq_ismi ?? $user->toliq_ismi ?? '-',
                'talaba_id' => $user->Talaba_ID ?? '-',
                'joriy'     => is_numeric($g->joriy_baho) ? (float) $g->joriy_baho : 0,
                'oraliq'    => is_numeric($g->oraliq_baho) ? (float) $g->oraliq_baho : 0,
                'reyting'   => is_numeric($g->joriy_oraliq) ? (float) $g->joriy_oraliq : 0,
                'yakuniy'   => is_numeric($g->yakuniy_baho) ? (float) $g->yakuniy_baho : 0,
                'umumiy'    => is_numeric($g->umumiy) ? (float) $g->umumiy : 0,
            ];
        }

        ksort($grouped);

        return $grouped;
    }

    /**
     * Umumiy baho (foiz) asosida "Raqamli ekvivalent"ni hisoblaydi.
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
     * Talabaning klassik "5/4/3/2/Kelmadi" bahosini aniqlaydi.
     *
     * Qoida:
     * - Barcha ballari (joriy, oraliq, reyting, yakuniy, umumiy) 0 bo'lsa -> "Kelmadi"
     *   (bu holat odatda talaba imtihonga umuman qatnashmaganini bildiradi)
     * - Aks holda Raqamli ekvivalentga qarab: 4.5 -> "5"; 4.0 -> "4"; 3.5/3.0 -> "3";
     *   2.5/2.0 -> "2"; 0 (lekin qatnashgan) -> "2"
     */
    private function calcClassic(array $s): string
    {
        $allZero = $s['joriy'] == 0 && $s['oraliq'] == 0 && $s['reyting'] == 0
            && $s['yakuniy'] == 0 && $s['umumiy'] == 0;

        if ($allZero) {
            return 'Kelmadi';
        }

        $scale = $this->calcScale($s['umumiy']);

        if ($scale >= 4.5) return '5';
        if ($scale <= 0) return '2';

        return (string) (int) floor($scale);
    }

    /**
     * Umumiy baho (foiz) asosida "Harfiy ekvivalent"ni hisoblaydi.
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
     * Umumiy baho (foiz) asosida "An'anaviy baho"ni hisoblaydi.
     */
    private function calcAnan($umumiy)
    {
        $u = (float) $umumiy;
        if ($u >= 70) return 'Yaxshi';
        if ($u >= 60) return 'Qoniqarli';
        return 'Qoniqarsiz';
    }

    /**
     * PREVIEW / TAHRIRLASH SAHIFASI.
     */
    public function form(subject $subject)
    {
        $grouped = $this->getStudentsByGroup($subject);
        $groups  = array_keys($grouped);

        $defaults = [
            'fakultet'      => '',
            'kafedra'       => '',
            'fan_krediti'   => '',
            'fan_oqituvchi' => optional($subject->teacher)->{"To‘liq_ismi"}
                ?? optional($subject->teacher)->Toliq_ismi
                ?? optional($subject->teacher)->toliq_ismi
                ?? '',
            'talim_tili'    => "O'zbek",
            'semestr'       => $subject->semster ? $subject->semster . '-semestr' : '',
        ];

        return view('subject.vedomost', [
            'subject'  => $subject,
            'grouped'  => $grouped,
            'groups'   => $groups,
            'defaults' => $defaults,
        ]);
    }

    /**
     * Bitta guruh uchun Spreadsheet obyektini quradi (shablonsiz, kod orqali).
     */
    private function buildSheetForGroup(subject $subject, string $guruh, array $students, array $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(12);
        $sheet = $spreadsheet->getActiveSheet();

        $safeTitle = mb_substr(preg_replace('/[^A-Za-z0-9\-]/', '_', $guruh), 0, 31);
        $sheet->setTitle($safeTitle ?: 'Guruh');

        // Faqat "Talaba" va "Talaba ID" ustunlari matn uzunligiga qarab dinamik kengayadi.
        // Qolgan (raqamli/qisqa) ustunlar kichik va qat'iy kenglikda qoladi - shunda
        // jadval umumiy A4 sahifasiga yaxshi sig'adi.
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(9);
        $sheet->getColumnDimension('E')->setWidth(9);
        $sheet->getColumnDimension('F')->setWidth(8);
        $sheet->getColumnDimension('G')->setWidth(9);
        $sheet->getColumnDimension('H')->setWidth(9);
        $sheet->getColumnDimension('I')->setWidth(9);
        $sheet->getColumnDimension('J')->setWidth(9);
        $sheet->getColumnDimension('K')->setWidth(11);

        // --- CHOP ETISH (PRINT) UCHUN SOZLAMALAR ---
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);
        $sheet->setPrintGridlines(false);
        $sheet->setShowGridlines(false);

        // --- SARLAVHA ---
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', "QO'QON UNIVERSITETI");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'BAHOLASH QAYDNOMASI' . ($subject->semster ? " ({$subject->semster}-semestr)" : ''));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 1-qator: Fakultet, Kafedra, Guruh
        $sheet->mergeCells('A4:K4');
        $sheet->setCellValue('A4', "Fakultet: {$data['fakultet']}, Kafedra: {$data['kafedra']}, Guruh: {$guruh}");

        // 2-qator: Fan, Fan krediti
        $sheet->mergeCells('A5:K5');
        $sheet->setCellValue('A5', "Fan: {$subject->nomi}, Fan krediti: {$data['fan_krediti']}");

        // 3-qator: Fan o'qituvchisi
        $sheet->mergeCells('A6:K6');
        $sheet->setCellValue('A6', "Fan o'qituvchisi: {$data['fan_oqituvchi']}");

        // 4-qator: Ta'lim tili
        $sheet->mergeCells('A7:K7');
        $sheet->setCellValue('A7', "Ta'lim tili: {$data['talim_tili']}");

        // 5-qator: Semestr
        $sheet->mergeCells('A8:K8');
        $sheet->setCellValue('A8', "Semestr: {$data['semestr']}");

        // --- JADVAL SARLAVHASI (asl shablondagi kabi 2 qatorli, faqat Guruh o'rniga Talaba ID) ---
        $headerRow  = 10;
        $headerRow2 = 11;

        $sheet->setCellValue("A{$headerRow}", '№ T/r');
        $sheet->setCellValue("B{$headerRow}", 'Talaba');
        $sheet->setCellValue("C{$headerRow}", 'Talaba ID');
        $sheet->setCellValue("D{$headerRow}", 'Semestr uchun reyting balli');
        $sheet->setCellValue("G{$headerRow}", 'Yakuniy nazorat');
        $sheet->setCellValue("H{$headerRow}", 'Umumiy baho');
        $sheet->setCellValue("I{$headerRow}", 'Raqamli ekvivalent');
        $sheet->setCellValue("J{$headerRow}", 'Harfiy ekvivalent');
        $sheet->setCellValue("K{$headerRow}", "An'anaviy baho");

        $sheet->setCellValue("D{$headerRow2}", 'Joriy nazorat');
        $sheet->setCellValue("E{$headerRow2}", 'Oraliq nazorat');
        $sheet->setCellValue("F{$headerRow2}", 'Reyting');

        $sheet->mergeCells("A{$headerRow}:A{$headerRow2}");
        $sheet->mergeCells("B{$headerRow}:B{$headerRow2}");
        $sheet->mergeCells("C{$headerRow}:C{$headerRow2}");
        $sheet->mergeCells("D{$headerRow}:F{$headerRow}");
        $sheet->mergeCells("G{$headerRow}:G{$headerRow2}");
        $sheet->mergeCells("H{$headerRow}:H{$headerRow2}");
        $sheet->mergeCells("I{$headerRow}:I{$headerRow2}");
        $sheet->mergeCells("J{$headerRow}:J{$headerRow2}");
        $sheet->mergeCells("K{$headerRow}:K{$headerRow2}");

        $sheet->getStyle("A{$headerRow}:K{$headerRow2}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:K{$headerRow2}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle("A{$headerRow}:K{$headerRow2}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension($headerRow)->setRowHeight(22);
        $sheet->getRowDimension($headerRow2)->setRowHeight(22);

        // --- MA'LUMOTLAR QATORLARI ---
        $row = $headerRow2 + 1;
        $counts = ['5' => 0, '4' => 0, '3' => 0, '2' => 0, 'Kelmadi' => 0];

        foreach ($students as $i => $s) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $s['ismi']);
            // Talaba ID matn (text) sifatida yoziladi - aks holda Excel uzun raqamni
            // ilmiy formatda (masalan 4.11221E+11) ko'rsatib qo'yadi
            $sheet->setCellValueExplicit(
                "C{$row}",
                (string) $s['talaba_id'],
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode(
                \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT
            );
            $sheet->setCellValue("D{$row}", $s['joriy']);
            $sheet->setCellValue("E{$row}", $s['oraliq']);
            $sheet->setCellValue("F{$row}", $s['reyting']);
            $sheet->setCellValue("G{$row}", $s['yakuniy']);
            $sheet->setCellValue("H{$row}", $s['umumiy']);
            $sheet->setCellValue("I{$row}", number_format($this->calcScale($s['umumiy']), 1));
            $sheet->setCellValue("J{$row}", $this->calcLetter($s['umumiy']));
            $sheet->setCellValue("K{$row}", $this->calcAnan($s['umumiy']));

            $classic = $this->calcClassic($s);
            $counts[$classic]++;

            $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $row++;
        }

        // --- FOOTER: Jami talabalar ---
        $row += 1;
        $sheet->mergeCells("A{$row}:K{$row}");
        $total = count($students);
        $sheet->setCellValue(
            "A{$row}",
            "Jami talabalar: {$total}, shundan, \"5\": {$counts['5']}, \"4\": {$counts['4']}, " .
                "\"3\": {$counts['3']}, \"2\": {$counts['2']}, \"Kelmadi\": {$counts['Kelmadi']}"
        );

        // --- IMZO: faqat o'qituvchi uchun, chiziq bilan ---
        $row += 3;
        $sheet->setCellValue("A{$row}", "O'qituvchi:");
        $sheet->setCellValue("B{$row}", $data['fan_oqituvchi']);
        $sheet->mergeCells("H{$row}:K{$row}");
        $sheet->setCellValue("H{$row}", '_________________________');
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Chop etishda har sahifada 1-11 qatorlar (sarlavha + jadval sarlavhasi) takrorlansin
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $headerRow2);
        $sheet->getPageSetup()->setPrintArea("A1:K{$row}");

        return $spreadsheet;
    }

    /**
     * Barcha guruhlar uchun alohida-alohida xlsx yaratib, ZIP qilib yuklab beradi.
     */
    public function exportAll(Request $request, subject $subject)
    {
        $data = $request->validate([
            'fakultet'      => 'nullable|string|max:255',
            'kafedra'       => 'nullable|string|max:255',
            'fan_krediti'   => 'nullable|string|max:255',
            'fan_oqituvchi' => 'nullable|string|max:255',
            'talim_tili'    => 'nullable|string|max:255',
            'semestr'       => 'nullable|string|max:255',
        ]);

        $grouped = $this->getStudentsByGroup($subject);

        if (empty($grouped)) {
            return response()->json(['message' => "Bu fan uchun baholar topilmadi"], 404);
        }

        $tmpDir = storage_path('app' . DIRECTORY_SEPARATOR . 'tmp_qaydnoma_' . uniqid());
        mkdir($tmpDir, 0777, true);

        $files = [];

        foreach ($grouped as $guruh => $students) {
            $spreadsheet = $this->buildSheetForGroup($subject, $guruh, $students, $data);
            $writer = new Xlsx($spreadsheet);

            $safeGuruh = preg_replace('/[^A-Za-z0-9\-]/', '_', $guruh);
            $filename  = "Baholash_qaydnomasi_{$safeGuruh}.xlsx";
            $path      = $tmpDir . DIRECTORY_SEPARATOR . $filename;

            $writer->save($path);
            $files[] = $path;

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $writer);
        }

        $zipName = 'Baholash_qaydnomalari_' . \Illuminate\Support\Str::slug($subject->nomi) . '.zip';
        $zipPath = $tmpDir . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Ichidagi vaqtinchalik xlsx fayllarni tozalaymiz (ZIP ichida saqlanib qoldi)
        foreach ($files as $file) {
            @unlink($file);
        }

        // Javob yuborilgandan keyin vaqtinchalik papkani ham tozalaymiz
        app()->terminating(function () use ($tmpDir) {
            @rmdir($tmpDir);
        });

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }
}