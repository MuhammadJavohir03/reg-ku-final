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
     * PREVIEW / TAHRIRLASH SAHIFASI.
     */
    public function form(subject $subject)
    {
        $grouped = $this->getStudentsByGroup($subject);
        $groups  = array_keys($grouped);

        $defaults = [
            'fakultet'       => optional($subject->fakultet)->nomi ?? '',
            'kafedra'        => optional($subject->kafedra)->nomi ?? '',
            'oquv_yili'      => optional($subject->oquv_yili)->nomi ?? '',
            'fan_krediti'    => $subject->kredit ?? '',
            'fan_oqituvchi'  => optional($subject->teacher)->{"To‘liq_ismi"}
                ?? optional($subject->teacher)['To‘liq_ismi']
                ?? optional($subject->teacher)['To‘liq_ismi']
                ?? '',
            'talim_tili'     => $subject->talim_tili,
            'semestr'        => $subject->semster ? $subject->semster . '-semestr' : '',
            'kafedra_mudiri' => $this->mudirFor($subject),

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
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(20);
        $sheet = $spreadsheet->getActiveSheet();

        $safeTitle = mb_substr(preg_replace('/[^A-Za-z0-9\-]/', '_', $guruh), 0, 31);
        $sheet->setTitle($safeTitle ?: 'Guruh');

        // "Talaba" ustuni (B) qat'iy 45 birlik kenglikda, matn sig'masa pastga
        // o'tadi (wrap text). "Talaba ID" (C) esa matn uzunligiga qarab dinamik
        // kengayadi. Qolgan (raqamli/qisqa) ustunlar kichik va qat'iy kenglikda
        // qoladi - shunda jadval umumiy A4 sahifasiga yaxshi sig'adi.
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(45);
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
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(20);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'BAHOLASH QAYDNOMASI' . ($subject->semster ? " ({$subject->semster}-semestr)" : ''));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(20);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 1-qator: Fakultet, Kafedra, Guruh
        $fakultetNomi = optional($subject->fakultet)->nomi ?? ($data['fakultet'] ?? '');
        $kafedraNomi  = optional($subject->kafedra)->nomi ?? ($data['kafedra'] ?? '');
        $fanKrediti   = $subject->kredit ?? ($data['fan_krediti'] ?? '');
        $oquv_yili    = optional($subject->oquv_yili)->nomi ?? ($data['oquv_yili'] ?? '');
        $fanOqituvchi = $data['fan_oqituvchi'] ?? '';
        $talimTili    = $data['talim_tili'] ?? '';

        $sheet->mergeCells('A4:K4');
        $sheet->setCellValue('A4', "Fakultet: {$fakultetNomi}, Kafedra: {$kafedraNomi}, Guruh: {$guruh}");

        // 2-qator: Fan, Fan krediti
        $sheet->mergeCells('A5:K5');
        $sheet->setCellValue('A5', "Fan: {$subject->nomi}, Fan krediti: {$fanKrediti}");

        // 3-qator: Fan o'qituvchisi
        $sheet->mergeCells('A6:K6');
        $sheet->setCellValue('A6', "Fan o'qituvchisi: {$fanOqituvchi}");

        // 4-qator: Ta'lim tili
        $sheet->mergeCells('A7:K7');
        $sheet->setCellValue('A7', "Ta'lim tili: {$talimTili}");

        // 5-qator: Semestr
        $sheet->mergeCells('A8:K8');
        $sheet->setCellValue('A8', "O'quv yili: {$oquv_yili}");

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
        $sheet->setCellValue("K{$headerRow}", "Imzo");

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

        $sheet->getStyle("A{$headerRow}:K{$headerRow2}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$headerRow}:K{$headerRow2}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle("A{$headerRow}:K{$headerRow2}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension($headerRow)->setRowHeight(60);
        $sheet->getRowDimension($headerRow2)->setRowHeight(60);

        // --- MA'LUMOTLAR QATORLARI ---
        $row = $headerRow2 + 1;

        // Kalitlar calcLetter() qaytaradigan qiymatlar bilan bir xil bo'lishi SHART:
        // 'A+', 'A', 'B+', 'B', 'C+', 'C', 'F'
        $counts = ['A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'F' => 0];

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
            $sheet->setCellValue("K{$row}", ''); // Imzo uchun bo'sh joy

            $key = $this->calcLetter($s['umumiy']);
            $counts[$key] = ($counts[$key] ?? 0) + 1;

            $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}:K{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(60);

            $row++;
        }

        // --- FOOTER: Jami talabalar ---
        $row += 1;
        $sheet->mergeCells("A{$row}:K{$row}");
        $total = count($students);
        $sheet->setCellValue(
            "A{$row}",
            "Jami talabalar: {$total}, shundan, \"A+ 95-100\": {$counts['A+']}, \"A 90-94\": {$counts['A']}, " .
                "\"B+ 80-89\": {$counts['B+']}, \"B 70-79\": {$counts['B']}, \"C+ 65-69\": {$counts['C+']}, " .
                "\"C 60-64\": {$counts['C']}, \"F 0-59\": {$counts['F']}"
        );

        // --- IMZO: Registrator ofisi boshlig'i uchun, chiziq bilan ---
        $row += 3;
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", "Registrator ofisi boshlig'i:");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);

        // Imzo chizig'i (bo'sh, faqat pastki chegara chiziq bo'lib ko'rinadi)
        $sheet->mergeCells("D{$row}:H{$row}");
        $sheet->getStyle("D{$row}:H{$row}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

        // F.I.Sh.
        $sheet->mergeCells("I{$row}:K{$row}");
        $sheet->setCellValue("I{$row}", "M.Ikramov");
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Chiziq ostiga kichik "(imzo)" izohi
        $row += 1;
        $sheet->mergeCells("D{$row}:H{$row}");
        $sheet->setCellValue("D{$row}", "(imzo)");
        $sheet->getStyle("D{$row}")->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- IMZO: Kafedra mudiri (registrator ofisi boshlig'i tagida) ---
        // Fanning kafedrasi va o'quv yiliga mos mudir "mudirlar" jadvalidan avtomatik olinadi.
        $row += 2;
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", "Kafedra mudiri:");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);

        $sheet->mergeCells("D{$row}:H{$row}");
        $sheet->getStyle("D{$row}:H{$row}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

        $sheet->mergeCells("I{$row}:K{$row}");
        $sheet->setCellValue("I{$row}", $this->mudirFor($subject));
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row += 1;
        $sheet->mergeCells("D{$row}:H{$row}");
        $sheet->setCellValue("D{$row}", "(imzo)");
        $sheet->getStyle("D{$row}")->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($headerRow, $headerRow2);
        $sheet->getPageSetup()->setPrintArea("A1:K{$row}");

        return $spreadsheet;
    }

    /**
     * Fanning kafedrasi va o'quv yiliga mos "mudirlar" jadvalidagi mudir F.I.Sh.ni qaytaradi.
     * Mos yozuv topilmasa - bo'sh satr (imzo joyi bo'sh qoladi, xato bermaydi).
     */
    private function mudirFor(subject $subject): string
    {
        if (!$subject->kafedra_id || !$subject->oquv_yili_id) {
            return '';
        }

        $fullName = \App\Models\Mudir::where('kafedra_id', $subject->kafedra_id)
            ->where('oquv_yili_id', $subject->oquv_yili_id)
            ->value('mudir');

        return \App\Models\Mudir::formatSignature($fullName);
    }

    /**
     * Fan uchun har bir guruhga alohida xlsx fayl yozadi va yozilgan fayl
     * yo'llarini qaytaradi. Fan/guruh uchun ma'lumot topilmasa bo'sh massiv qaytadi.
     * ($subject, $data, $dir) - VedomostController ichida bir marta yozilgan,
     * bitta fan uchun ham, bulk (hammasi) eksport uchun ham ishlatiladi.
     */
    private function writeGroupExcelFiles(subject $subject, array $grouped, array $data, string $dir): array
    {
        $files = [];

        foreach ($grouped as $guruh => $students) {
            $spreadsheet = $this->buildSheetForGroup($subject, $guruh, $students, $data);
            $writer = new Xlsx($spreadsheet);

            $safeGuruh = preg_replace('/[^A-Za-z0-9\-]/', '_', $guruh);
            $filename  = "{$safeGuruh}.xlsx";
            $path      = $dir . DIRECTORY_SEPARATOR . $filename;

            $writer->save($path);
            $files[] = $path;

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $writer);
        }

        return $files;
    }

    /**
     * Berilgan fayllarni ko'rsatilgan yo'ldagi ZIP arxiviga yig'adi.
     */
    private function zipFiles(array $files, string $zipPath): void
    {
        $zip = new ZipArchive();
        $openResult = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            throw new \RuntimeException("ZIP faylni yaratib bo'lmadi (kod: {$openResult})");
        }
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
    }

    /**
     * Bitta fan uchun standart (subject'ning o'zidan olingan) ma'lumotlar
     * to'plamini quradi - form() dagi $defaults bilan bir xil mantiq.
     * Bulk (hammasini) eksport qilishda foydalanuvchi har bir fan uchun
     * qo'lda maydon to'ldirmaydi, shuning uchun subject'ning o'z
     * bog'lanishlaridan (teacher/talim_tili/oquv_yili) foydalaniladi.
     */
    private function defaultDataForSubject(subject $subject): array
    {
        return [
            'fan_oqituvchi' => optional($subject->teacher)->{"To‘liq_ismi"}
                ?? optional($subject->teacher)['To‘liq_ismi']
                ?? '',
            'talim_tili' => $subject->talim_tili ?? '',
            'oquv_yili'  => optional($subject->oquv_yili)->nomi ?? '',
        ];
    }

    /**
     * Barcha guruhlar uchun alohida-alohida xlsx yaratib, ZIP qilib yuklab beradi.
     */
    public function exportAll(Request $request, subject $subject)
    {
        $data = $request->validate([
            'fan_oqituvchi' => 'nullable|string|max:255',
            'talim_tili'    => 'nullable|string|max:255',
            'oquv_yili'     => 'nullable|string|max:255',
        ]);

        // Validate() faqat requestda kelgan kalitlarni qaytaradi -
        // shuning uchun bo'sh qoldirilgan maydonlar uchun standart qiymatlar beramiz.
        $data['fan_oqituvchi'] = $data['fan_oqituvchi'] ?? '';
        $data['talim_tili']    = $data['talim_tili'] ?? '';
        $data['oquv_yili']     = $data['oquv_yili'] ?? '';

        $grouped = $this->getStudentsByGroup($subject);

        if (empty($grouped)) {
            return response()->json(['message' => "Bu fan uchun baholar topilmadi"], 404);
        }

        $tmpDir = storage_path('app' . DIRECTORY_SEPARATOR . 'tmp_qaydnoma_' . uniqid());
        mkdir($tmpDir, 0777, true);

        $files = [];

        try {
            $files = $this->writeGroupExcelFiles($subject, $grouped, $data, $tmpDir);

            $zipName = \Illuminate\Support\Str::slug($subject->nomi) . '.zip';
            $zipPath = $tmpDir . DIRECTORY_SEPARATOR . $zipName;

            $this->zipFiles($files, $zipPath);
        } catch (\Throwable $e) {
            // Xatolik bo'lsa vaqtinchalik fayllarni tozalab, xatoni loglaymiz va foydalanuvchiga aniq xabar qaytaramiz
            foreach ($files as $file) {
                @unlink($file);
            }
            @rmdir($tmpDir);

            \Illuminate\Support\Facades\Log::error('Vedomost export xatosi: ' . $e->getMessage(), [
                'subject_id' => $subject->id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => "Export vaqtida xatolik yuz berdi: " . $e->getMessage(),
            ], 500);
        }

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

    /**
     * index() dagi bilan bir xil filterlarni qo'llab, baholari mavjud fanlar
     * ro'yxatini qaytaradi. Bulk (hammasini) eksportning barcha bosqichlarida
     * (start/step) shu bitta joydan foydalaniladi - filterlar ikki joyda
     * turlicha yozilib, chalkashib ketmasligi uchun.
     */
    private function filteredSubjectsForBulkExport(Request $request)
    {
        $search     = $request->get('search');
        $categoryId = $request->get('category_id');
        $kurs       = $request->get('kurs');
        $semester   = $request->get('semster');

        return subject::select('id', 'nomi')
            ->whereHas('grades')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomi', 'like', "%{$search}%")
                        ->orWhere('semster', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($q3) use ($search) {
                            $q3->where('guruh', 'like', "%{$search}%");
                        })
                        ->orWhereHas('teacher', function ($q2) use ($search) {
                            $q2->where('To‘liq_ismi', 'like', "%{$search}%");
                        });
                });
            })
            ->when($categoryId, fn($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($semester, fn($query, $semester) => $query->where('semster', $semester))
            ->when($kurs, function ($query, $kurs) {
                $startSem = ($kurs - 1) * 2 + 1;
                $endSem = $kurs * 2;
                $query->whereBetween('semster', [$startSem, $endSem]);
            })
            ->orderBy('nomi')
            ->get();
    }

    /**
     * Bulk-eksport uchun batch papka yo'lini quradi. $batch faqat harf/raqamdan
     * iborat bo'lishi shart (route'da regex bilan ham cheklangan) - shu orqali
     * path traversal xavfsizligi ta'minlanadi.
     */
    private function batchDir(string $batch): string
    {
        return storage_path('app' . DIRECTORY_SEPARATOR . 'tmp_qaydnoma_batch_' . $batch);
    }

    /**
     * 1-BOSQICH: BARCHA FANLAR uchun bulk eksportni boshlaydi.
     * Filterlarga mos, baholari mavjud fanlar ro'yxatini va yangi batch_id'ni qaytaradi.
     * Frontend keyin har bir fan uchun alohida "step" so'rovi yuboradi -
     * shu orqali haqiqiy progress (X / N) ko'rsatish mumkin bo'ladi.
     */
    public function exportAllStart(Request $request)
    {
        $subjects = $this->filteredSubjectsForBulkExport($request);

        if ($subjects->isEmpty()) {
            return response()->json(['message' => "Baholari mavjud fanlar topilmadi"], 404);
        }

        $batch = \Illuminate\Support\Str::random(24);
        $dir = $this->batchDir($batch);
        mkdir($dir, 0777, true);
        mkdir($dir . DIRECTORY_SEPARATOR . 'ziplar', 0777, true);

        // Ruxsat etilgan fan id'lari shu faylga yoziladi - "step" bosqichida
        // faqat shu ro'yxatdagi id'lar bilan ishlash mumkin (xavfsizlik uchun).
        file_put_contents(
            $dir . DIRECTORY_SEPARATOR . 'manifest.json',
            json_encode(['subject_ids' => $subjects->pluck('id')->values()->all()])
        );

        return response()->json([
            'batch'    => $batch,
            'subjects' => $subjects->map(fn($s) => ['id' => $s->id, 'nomi' => $s->nomi])->values(),
            'total'    => $subjects->count(),
        ]);
    }

    /**
     * 2-BOSQICH: bitta fan uchun guruhlar bo'yicha xlsx fayllarni tayyorlab,
     * shu fan-zip'ini batch papkasiga yozadi. Frontend har bir fan uchun
     * shu endpointni ketma-ket chaqiradi va javobga qarab progressni yangilaydi.
     */
    public function exportAllStep(Request $request, string $batch, subject $subject)
    {
        $dir = $this->batchDir($batch);
        $manifestPath = $dir . DIRECTORY_SEPARATOR . 'manifest.json';

        if (!is_dir($dir) || !is_file($manifestPath)) {
            return response()->json(['message' => "Sessiya topilmadi yoki muddati tugagan. Iltimos, eksportni qaytadan boshlang."], 404);
        }

        $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
        $allowedIds = $manifest['subject_ids'] ?? [];

        if (!in_array($subject->id, $allowedIds, true)) {
            return response()->json(['message' => "Bu fan ushbu eksport sessiyasiga tegishli emas"], 403);
        }

        try {
            $grouped = $this->getStudentsByGroup($subject);

            if (empty($grouped)) {
                return response()->json(['exported' => false, 'nomi' => $subject->nomi]);
            }

            $subjectTmpDir = $dir . DIRECTORY_SEPARATOR . 'fan_' . $subject->id;
            mkdir($subjectTmpDir, 0777, true);

            $data = $this->defaultDataForSubject($subject);
            $groupFiles = $this->writeGroupExcelFiles($subject, $grouped, $data, $subjectTmpDir);

            $safeName = (\Illuminate\Support\Str::slug($subject->nomi) ?: 'fan') . '-' . $subject->id;
            $subjectZipPath = $dir . DIRECTORY_SEPARATOR . 'ziplar' . DIRECTORY_SEPARATOR . $safeName . '.zip';
            $this->zipFiles($groupFiles, $subjectZipPath);

            foreach ($groupFiles as $f) {
                @unlink($f);
            }
            @rmdir($subjectTmpDir);

            return response()->json([
                'exported'    => true,
                'nomi'        => $subject->nomi,
                'guruh_soni'  => count($grouped),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vedomost bulk-export step xatosi: ' . $e->getMessage(), [
                'batch'      => $batch,
                'subject_id' => $subject->id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => "\"{$subject->nomi}\" fani uchun xatolik: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 3-BOSQICH: barcha "step"lar tugagach chaqiriladi. Batch papkasidagi
     * fan-ziplarni bitta umumiy ZIP ichiga yig'ib, yuklab beradi
     * (zip -> ziplar -> excellar tuzilishi) va vaqtinchalik papkani tozalaydi.
     */
    public function exportAllFinish(Request $request, string $batch)
    {
        $dir = $this->batchDir($batch);
        $zipsDir = $dir . DIRECTORY_SEPARATOR . 'ziplar';

        if (!is_dir($zipsDir)) {
            return response()->json(['message' => "Sessiya topilmadi yoki muddati tugagan"], 404);
        }

        $subjectZipPaths = glob($zipsDir . DIRECTORY_SEPARATOR . '*.zip') ?: [];

        if (empty($subjectZipPaths)) {
            $this->cleanupBatchDir($dir);
            return response()->json(['message' => "Hech bir fan uchun baholar topilmadi"], 404);
        }

        $masterZipName = 'Barcha_vedomostlar_' . now()->format('Y-m-d_His') . '.zip';
        $masterZipPath = $dir . DIRECTORY_SEPARATOR . $masterZipName;

        try {
            $this->zipFiles($subjectZipPaths, $masterZipPath);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vedomost bulk-export finish xatosi: ' . $e->getMessage(), [
                'batch' => $batch,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => "Umumiy ZIP yaratishda xatolik: " . $e->getMessage()], 500);
        }

        foreach ($subjectZipPaths as $f) {
            @unlink($f);
        }
        @rmdir($zipsDir);
        @unlink($dir . DIRECTORY_SEPARATOR . 'manifest.json');

        app()->terminating(function () use ($dir) {
            @rmdir($dir);
        });

        return response()->download($masterZipPath, $masterZipName)->deleteFileAfterSend(true);
    }

    private function cleanupBatchDir(string $dir): void
    {
        $zipsDir = $dir . DIRECTORY_SEPARATOR . 'ziplar';
        foreach (glob($zipsDir . DIRECTORY_SEPARATOR . '*.zip') ?: [] as $f) {
            @unlink($f);
        }
        @rmdir($zipsDir);
        @unlink($dir . DIRECTORY_SEPARATOR . 'manifest.json');
        @rmdir($dir);
    }
}