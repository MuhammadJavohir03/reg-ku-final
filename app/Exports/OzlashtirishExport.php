<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class OzlashtirishExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $talabalar;
    protected $fanlar;

    public function __construct($talabalar, $fanlar)
    {
        $this->talabalar = $talabalar;
        $this->fanlar    = $fanlar;
    }

    public function array(): array
    {
        $rows = [];

        // 1-qator: fan nomlari
        $header1 = ['№', 'Talaba F.I.O', 'Guruh'];
        foreach ($this->fanlar as $fan) {
            $header1[] = $fan->nomi;
            $header1[] = '';
            $header1[] = '';
        }
        $rows[] = $header1;

        // 2-qator: J/O, U, D
        $header2 = ['', '', ''];
        foreach ($this->fanlar as $fan) {
            $header2[] = 'J/O';
            $header2[] = 'U';
            $header2[] = 'D';
        }
        $rows[] = $header2;

        // Ma'lumotlar
        foreach ($this->talabalar as $i => $talaba) {
            $row = [
                $i + 1,
                $talaba->{"To‘liq_ismi"} ?? $talaba->To‘liq_ismi ?? '-',
                $talaba->Guruh ?? '-',
            ];
            foreach ($this->fanlar as $fan) {
                $grade = $talaba->getMergedGrade($fan->id);
                $row[] = $grade?->joriy_oraliq ?? '-';
                $row[] = $grade?->umumiy       ?? '-';
                $row[] = $grade?->davomat !== null ? $grade->davomat . '%' : '-';
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $fanCount  = $this->fanlar->count();
        $lastColIndex = 3 + ($fanCount * 3);
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);
        $totalRows = count($this->talabalar) + 2;

        // 1-qator: fan header uslubi
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3C3489'],
            ],
            'font' => [
                'color' => ['rgb' => 'CECBF6'],
                'bold'  => true,
                'size'  => 10,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        // 2-qator: J/O, U, D header uslubi
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEDFE'],
            ],
            'font' => [
                'color' => ['rgb' => '3C3489'],
                'bold'  => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Ma'lumot qatorlari
        $sheet->getStyle("A3:{$lastCol}{$totalRows}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E0E0E0'],
                ],
            ],
        ]);

        // Talaba ismi chap hizalanish
        $sheet->getStyle("B3:B{$totalRows}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Qizil kataklar
        foreach ($this->talabalar as $i => $talaba) {
            $rowNum = $i + 3;
            foreach ($this->fanlar as $j => $fan) {
                $grade = $talaba->getMergedGrade($fan->id);

                $joCol = Coordinate::stringFromColumnIndex(4 + ($j * 3));
                $uCol  = Coordinate::stringFromColumnIndex(5 + ($j * 3));
                $dCol  = Coordinate::stringFromColumnIndex(6 + ($j * 3));

                if ($grade && $grade->joriy_oraliq !== null && $grade->joriy_oraliq < 20) {
                    $sheet->getStyle("{$joCol}{$rowNum}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCEBEB']],
                        'font' => ['color' => ['rgb' => '791F1F'], 'bold' => true],
                    ]);
                }

                if ($grade && $grade->umumiy !== null && $grade->umumiy < 60) {
                    $sheet->getStyle("{$uCol}{$rowNum}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCEBEB']],
                        'font' => ['color' => ['rgb' => '791F1F'], 'bold' => true],
                    ]);
                }

                if ($grade && $grade->davomat !== null && $grade->davomat >= 33) {
                    $sheet->getStyle("{$dCol}{$rowNum}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCEBEB']],
                        'font' => ['color' => ['rgb' => '791F1F'], 'bold' => true],
                    ]);
                }

                // Yashil qator — hech qanday qizil yo'q
                $qarzdor = ($grade && (
                    ($grade->joriy_oraliq !== null && $grade->joriy_oraliq < 20) ||
                    ($grade->umumiy !== null && $grade->umumiy < 60) ||
                    ($grade->davomat !== null && $grade->davomat >= 33)
                ));

                if (!$qarzdor && $grade) {
                    $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EAF3DE']],
                    ]);
                }
            }
        }

        // Qator balandliklari
        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getRowDimension(2)->setRowHeight(20);

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,
            'B' => 35,
            'C' => 10,
        ];

        foreach ($this->fanlar as $i => $fan) {
            $jo = Coordinate::stringFromColumnIndex(4 + ($i * 3));
            $u  = Coordinate::stringFromColumnIndex(5 + ($i * 3));
            $d  = Coordinate::stringFromColumnIndex(6 + ($i * 3));
            $widths[$jo] = 7;
            $widths[$u]  = 7;
            $widths[$d]  = 7;
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $fanCount  = $this->fanlar->count();
                $lastColIndex = 3 + ($fanCount * 3);
                $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);

                // A1:C1 merge (№, Ism, Guruh)
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');

                // Fan nomlari merge
                foreach ($this->fanlar as $i => $fan) {
                    $start = 4 + ($i * 3);
                    $end   = $start + 2;
                    $startLetter = Coordinate::stringFromColumnIndex($start);
                    $endLetter   = Coordinate::stringFromColumnIndex($end);
                    $sheet->mergeCells("{$startLetter}1:{$endLetter}1");
                }

                // Hamma border
                $totalRows = count($this->talabalar) + 2;
                $sheet->getStyle("A1:{$lastCol}{$totalRows}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
