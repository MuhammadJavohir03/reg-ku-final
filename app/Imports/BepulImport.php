<?php

namespace App\Imports;

use App\Models\grade;
use App\Models\User;
use App\Models\subject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class BepulImport
{
    public int $yangilandi = 0;
    public int $bepulYangilandi = 0;
    public int $ballYangilandi = 0;
    public int $topilmadiGrade = 0;
    public int $talabaTopilmadi = 0;
    public int $fanTopilmadi = 0;
    public int $qaytaIshlanganKatak = 0;

    
    private array $subjectCache = [];

    
    private array $subjectOquvYili = [];

    
    private array $colSemester = [];
    private const SPECIAL_FIFTY_OQUV_YILI = [6, 7];

    public function import(string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $highestCol = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $this->colSemester = [];
        for ($col = 15; $col <= $highestCol; $col++) {
            if ($col <= 20) {
                $this->colSemester[$col] = 1;
            } elseif ($col <= 26) {
                $this->colSemester[$col] = 2;
            } elseif ($col <= 31) {
                $this->colSemester[$col] = 3;
            } elseif ($col <= 36) {
                $this->colSemester[$col] = 4;
            } elseif ($col <= 42) {
                $this->colSemester[$col] = 5;
            } else {
                $this->colSemester[$col] = 6;
            }
        }

        $subjectCols = [];
        for ($col = 15; $col <= $highestCol; $col++) {
            $header = $sheet->getCellByColumnAndRow($col, 3)->getValue();
            if ($header === null || trim((string) $header) === '') {
                continue;
            }
            $subjectCols[$col] = $this->normalizeSubjectName((string) $header);
        }

        if (empty($subjectCols)) {
            throw new \RuntimeException('Excel 3-qatorida fan nomlari topilmadi (15-ustundan boshlab).');
        }

        for ($row = 5; $row <= $highestRow; $row++) {
            $talabaId   = trim((string) ($sheet->getCellByColumnAndRow(8, $row)->getValue() ?? ''));
            $talabaIsmi = trim((string) ($sheet->getCellByColumnAndRow(10, $row)->getValue() ?? ''));

            if ($talabaId === '' && $talabaIsmi === '') {
                continue;
            }

            $user = $this->findUser($talabaId, $talabaIsmi);
            if (!$user) {
                Log::warning("[Bepul import] Talaba topilmadi: ID='{$talabaId}' Ism='{$talabaIsmi}'");
                $this->talabaTopilmadi++;
                continue;
            }

            foreach ($subjectCols as $col => $subjectName) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $raw  = $cell->getValue();

                if ($raw === null || $raw === '') {
                    continue;
                }

                $semestr   = $this->colSemester[$col] ?? null;
                $subjectId = $this->resolveSubjectId($subjectName, $semestr);
                if (!$subjectId) {
                    continue;
                }

                $oquvYiliId         = $this->subjectOquvYili[$subjectId] ?? null;
                $applySpecialFifty  = $oquvYiliId !== null
                    && in_array((int) $oquvYiliId, self::SPECIAL_FIFTY_OQUV_YILI, true);

                $excelBall = $this->parseBall($raw, $applySpecialFifty);
                if ($excelBall === null) {
                    continue;
                }

                $grade = grade::where('user_id', $user->id)
                    ->where('subject_id', $subjectId)
                    ->first();

                if (!$grade) {
                    $this->topilmadiGrade++;
                    continue;
                }

                $isRed   = $this->isRedColor($cell);
                $changed = false;

                $newBepul = $isRed ? 0 : 1;
                if ((int) $grade->bepul !== $newBepul) {
                    $grade->bepul = $newBepul;
                    $changed = true;
                    $this->bepulYangilandi++;
                }

                if (!$isRed) {
                    $umumiy = (float) ($grade->umumiy ?? 0);
                    if ($excelBall > $umumiy) {
                        $farq = $excelBall - $umumiy;
                        $grade->yakuniy_baho = (float) ($grade->yakuniy_baho ?? 0) + $farq;
                        $grade->umumiy = $excelBall;
                        $changed = true;
                        $this->ballYangilandi++;
                    }
                }

                if ($changed) {
                    $grade->save();
                    $this->yangilandi++;
                }

                $this->qaytaIshlanganKatak++;
            }
        }
    }

    
    private function parseBall($raw, bool $applySpecialFifty): ?float
    {
        if (is_numeric($raw)) {
            $value = (float) $raw;
            if ($applySpecialFifty && $value === 50.0) {
                return 60.0;
            }
            return $value;
        }

        $s = trim((string) $raw);

        if ($applySpecialFifty && preg_match('/^50\s*[@$]$/u', $s)) {
            return 60.0;
        }

        if (preg_match('/^60\s*[@$]$/u', $s)) {
            return 60.0;
        }

        if (preg_match('/^\d+([.,]\d+)?$/u', $s)) {
            return (float) str_replace(',', '.', $s);
        }

        return null;
    }

    private function isRedColor($cell): bool
    {
        $fill = $cell->getStyle()->getFill();
        $fillType = $fill->getFillType();

        if ($fillType === null
            || $fillType === \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE
        ) {
            return false;
        }

        $rgb = strtoupper((string) $fill->getStartColor()->getRGB());

        if (strlen($rgb) === 8) {
            $rgb = substr($rgb, 2);
        }

        if ($rgb === '' || strlen($rgb) !== 6 || $rgb === '000000' || $rgb === 'FFFFFF') {
            return false;
        }

        $r = hexdec(substr($rgb, 0, 2));
        $g = hexdec(substr($rgb, 2, 2));
        $b = hexdec(substr($rgb, 4, 2));

        if ($r >= 160 && $g <= 100 && $b <= 100) {
            return true;
        }

        $known = ['FF0000', 'C00000', 'FF5050', 'FF6B6B'];

        return in_array($rgb, $known, true);
    }

    private function findUser(string $talabaId, string $talabaIsmi): ?User
    {
        if ($talabaId !== '' && Schema::hasColumn('users', 'reyting_raqami')) {
            $user = User::where('reyting_raqami', $talabaId)->first();
            if ($user) {
                return $user;
            }
        }

        if ($talabaId !== '' && Schema::hasColumn('users', 'Talaba_ID')) {
            $user = User::where('Talaba_ID', $talabaId)->first();
            if ($user) {
                return $user;
            }
        }

        if ($talabaIsmi === '') {
            return null;
        }

        $user = User::where('To‘liq_ismi', $talabaIsmi)->first();
        if ($user) {
            return $user;
        }

        $normalized = $this->normalizeName($talabaIsmi);
        $candidates = User::where('To‘liq_ismi', 'like', mb_substr($talabaIsmi, 0, 12) . '%')
            ->limit(30)
            ->get();

        foreach ($candidates as $c) {
            if ($this->normalizeName($c->{'To‘liq_ismi'}) === $normalized) {
                return $c;
            }
        }

        return null;
    }

    private function resolveSubjectId(string $name, ?int $semestr): ?int
    {
        $cacheKey = $name . '|' . ($semestr ?? '');
        if (array_key_exists($cacheKey, $this->subjectCache)) {
            return $this->subjectCache[$cacheKey];
        }

        $candidates = $this->subjectNameCandidates($name);
        $subject = null;

        foreach ($candidates as $cand) {
            $q = subject::query();

            if (Schema::hasColumn('subjects', 'nomi')) {
                $q->where(function ($qq) use ($cand) {
                    $qq->where('nomi', $cand)
                        ->orWhereRaw('LOWER(nomi) = ?', [mb_strtolower($cand)]);
                });
            } elseif (Schema::hasColumn('subjects', 'name')) {
                $q->where(function ($qq) use ($cand) {
                    $qq->where('name', $cand)
                        ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($cand)]);
                });
            } else {
                break;
            }

            if ($semestr !== null && Schema::hasColumn('subjects', 'semster')) {
                $q->where('semster', $semestr);
            }

            $subject = $q->first();
            if ($subject) {
                break;
            }

            if ($semestr !== null && Schema::hasColumn('subjects', 'semster')) {
                $q2 = subject::query();
                if (Schema::hasColumn('subjects', 'nomi')) {
                    $q2->where(function ($qq) use ($cand) {
                        $qq->where('nomi', $cand)
                            ->orWhereRaw('LOWER(nomi) = ?', [mb_strtolower($cand)]);
                    });
                }
                $subject = $q2->first();
                if ($subject) {
                    break;
                }
            }
        }

        if (!$subject) {
            Log::warning("[Bepul import] Fan topilmadi: '{$name}' (semestr={$semestr})");
            $this->fanTopilmadi++;
            $this->subjectCache[$cacheKey] = null;
            return null;
        }

        $subjectId = (int) $subject->id;
        $this->subjectCache[$cacheKey] = $subjectId;

        if (Schema::hasColumn('subjects', 'oquv_yili_id')) {
            $this->subjectOquvYili[$subjectId] = $subject->oquv_yili_id !== null
                ? (int) $subject->oquv_yili_id
                : null;
        } else {
            $this->subjectOquvYili[$subjectId] = null;
        }

        return $subjectId;
    }

    private function subjectNameCandidates(string $name): array
    {
        $aliases = [
            'Xorijiy tili' => ['Xorijiy til', 'Xorijiy til (ingliz)', 'Xorijiy tili'],
            "Rus tili/O'zbek tili" => ["O‘zbek (rus) tili", "O'zbek (rus) tili", 'Rus tili'],
            'Siyosatshunoslikka kirish/ PUL KREDITI SIYOSATI (EKR)' => [
                'Siyosatshunoslikka kirish',
                'Pul kredit siyosati',
                'PUL KREDITI SIYOSATI (EKR)',
            ],
            'Malakaviy amaliyot' => ['Malakaviy amaliyot', 'Malaka amaliyoti', 'Amaliyot'],
            'Xorijiy til (ingliz)' => ['Xorijiy til (ingliz)', 'Xorijiy til', 'Xorijiy tili'],
        ];

        $list = [$name];

        if (isset($aliases[$name])) {
            $list = array_merge($list, $aliases[$name]);
        }

        if (str_contains($name, '/')) {
            foreach (preg_split('/\s*\/\s*/u', $name) as $p) {
                $p = trim($p);
                if ($p !== '') {
                    $list[] = $p;
                }
            }
        }

        $list[] = trim(preg_replace('/\s*\([^)]*\)\s*/u', '', $name));

        return array_values(array_unique(array_filter(array_map('trim', $list))));
    }

    private function normalizeSubjectName(string $name): string
    {
        $name = str_replace(["\r", "\n"], ' ', $name);
        $name = preg_replace('/\s+/u', ' ', $name);
        return trim($name);
    }

    private function normalizeName(string $text): string
    {
        $text = str_replace(["‘", "’", "`", "ʼ", "ʻ", "´", "′"], "'", $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim(mb_strtoupper($text, 'UTF-8'));
    }
}