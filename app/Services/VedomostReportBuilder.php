<?php

namespace App\Services;

use App\Models\subject;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * "Baholash qaydnomasi" (rasmiy vedomost) formatidagi Excel sahifasini quradi.
 *
 * MUHIM: bu klass VedomostController'dagi buildSheetForGroup()/calcScale()/calcLetter()
 * metodlaridan CO'CHIRILGAN (mantiq bitta baytga o'zgartirilmagan holda). Maqsad -
 * bir nechta joyda (Vedomost sahifasi, Jurnal eksporti va h.k.) chaqirilganda ham
 * har doim AYNAN BIR XIL Excel natija chiqishini kafolatlash.
 *
 * $students massividagi har bir element quyidagi kalitlarga ega bo'lishi SHART:
 *   'ismi', 'talaba_id', 'joriy', 'oraliq', 'reyting', 'yakuniy', 'umumiy'
 *
 * $data massivi (fallback/qo'shimcha ma'lumotlar):
 *   'fakultet', 'kafedra', 'fan_krediti', 'fan_oqituvchi', 'talim_tili', 'oquv_yili'
 */
class VedomostReportBuilder
{
    /**
     * Umumiy baho (foiz) asosida "Raqamli ekvivalent"ni hisoblaydi.
     */
    public static function calcScale($umumiy)
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
    public static function calcLetter($umumiy)
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
     * Bitta guruh uchun Spreadsheet obyektini quradi (shablonsiz, kod orqali).
     */
    public static function buildSheet(subject $subject, string $guruh, array $students, array $data = []): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(17);
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
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(20);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'BAHOLASH QAYDNOMASI' . ($subject->semster ? " ({$subject->semster}-semestr)" : ''));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(17);
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

        // --- JADVAL SARLAVHASI (2 qatorli) ---
        $headerRow  = 10;
        $headerRow2 = 11;

        $sheet->setCellValue("A{$headerRow}", '№ T/r');
        $sheet->setCellValue("B{$headerRow}", 'Talaba');
        $sheet->setCellValue("C{$headerRow}", 'Talaba ID');
        $sheet->setCellValue("D{$headerRow}", 'Semestr uchun reyting balli');
        $sheet->setCellValue("G{$headerRow}", 'Yakuniy nazorat');
        $sheet->setCellValue("H{$headerRow}", 'Umumiy baho (%)');
        $sheet->setCellValue("I{$headerRow}", 'Raqamli ekvivalent');
        $sheet->setCellValue("J{$headerRow}", 'Harfiy ekvivalent');
        $sheet->setCellValue("K{$headerRow}", 'Imzo');

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
        $sheet->getRowDimension($headerRow)->setRowHeight(34);
        $sheet->getRowDimension($headerRow2)->setRowHeight(34);

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
            $sheet->setCellValue("I{$row}", number_format(self::calcScale($s['umumiy']), 1));
            $sheet->setCellValue("J{$row}", self::calcLetter($s['umumiy']));
            $sheet->setCellValue("K{$row}", ''); // Imzo uchun bo'sh joy

            $key = self::calcLetter($s['umumiy']);
            $counts[$key] = ($counts[$key] ?? 0) + 1;

            $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}:K{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getRowDimension($row)->setRowHeight(34);

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

        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($headerRow, $headerRow2);
        $sheet->getPageSetup()->setPrintArea("A1:K{$row}");

        return $spreadsheet;
    }
}