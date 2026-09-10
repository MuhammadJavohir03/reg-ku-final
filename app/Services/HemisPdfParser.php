<?php

namespace App\Services;

use Symfony\Component\Process\Process;

/**
 * HEMIS PDF parser — ikkala formatni qo'llab-quvvatlaydi:
 *
 *  A) "Baholash qaydnomasi" (1-shakl) — reyting raqami bor, ism 2-3 qator
 *  B) "Guruh reyting qaydnomasi" — ism + ballar bitta qatorda, davomat bor
 */
class HemisPdfParser
{
    private const IGNORE_PATTERN = '/^(№|Talabaning|Reyting|daftarchasining|Semestrda|ballar|ΣJN|ΣON|ΣJN\+ΣON|Baho|imzosi|o[‘\'`ʼ]?qituvchi|ko[‘\'`ʼ]?rsatkichi|O[‘\'`ʼ]?zlashtirish|YN|raqami|Jami talabalar|Fakultet|Kafedra|QO[‘\'`ʼ]?QON|BAHOLASH|Fan |Fan:|Fan o|Yakuniy|1-shakl|TURG|dekani|mudiri|JN\s|ON\s|As\.|Guruh reyting|https:\/\/|Chop etish|Ro.yxat|Fanlar|Nazorat|Baholash tizimi|Kredit|Dastur)/ui';

    private const BAHOLASH_DATA_PATTERN = '/(?:^|\s)(\d{9,15})\s+([\d.]+)\s+([\d.]+)\s+([\d.]+)\s+([\d.]+)\s+([\d.]+)\s+([\d.]+)\s*$/u';

    public function parse(string $pdfPath): array
    {
        $text = $this->extractLayoutText($pdfPath);

        if (preg_match('/Guruh reyting qaydnomasi|Davomat/ui', $text)) {
            return $this->parseGuruhReyting($text);
        }

        return $this->parseBaholashQaydnomasi($text);
    }

    /**
     * Guruh reyting qaydnomasi (hemis.kokanduni.uz dan chop).
     * O'ng tomondagi meta-ustunlar (Fanlar, Nazorat turi...) tozalanadi.
     */
    private function parseGuruhReyting(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $rows  = [];

        foreach ($lines as $line) {
            $line = rtrim($line);

            // O'ng sidebar junk ni olib tashlash
            $line = preg_replace('/\s+(Nazorat\s+turi|YN\s+turi|Guruh\s|Baholash\s+tizimi|Fanlar\s|Dastur\s|Nazorat\s+sanasi).*$/ui', '', $line);

            if (trim($line) === '' || preg_match(self::IGNORE_PATTERN, trim($line))) {
                continue;
            }

            if (!preg_match('/^\s*(\d{1,3})\s+(.+)$/u', $line, $m)) {
                continue;
            }

            $rowNum = $m[1];
            $rest   = trim($m[2]);

            // Ism + sonlar
            if (!preg_match('/^((?:[^\d\s]|[^\d]\S)*[^\d\s])\s+((?:\d+\.?\d*\s*)+)$/u', $rest, $nm)) {
                if (!preg_match('/^(.+?)\s+((?:\d+\.?\d*\s*)+)$/u', $rest, $nm)) {
                    continue;
                }
                if (preg_match('/\d/u', $nm[1])) {
                    continue;
                }
            }

            $name = trim(preg_replace('/\s+/u', ' ', $nm[1]));
            $nums = array_map('floatval', preg_split('/\s+/u', trim($nm[2]), -1, PREG_SPLIT_NO_EMPTY));

            if (count($nums) < 3 || $name === '') {
                continue;
            }

            $jn = $on = $reyting = $yn = $umumiy = $davomat = 0.0;
            $count = count($nums);

            if ($count >= 6) {
                $jn = $nums[0]; $on = $nums[1]; $reyting = $nums[2];
                $yn = $nums[3]; $umumiy = $nums[4]; $davomat = $nums[5];
            } elseif ($count === 5) {
                $sumCheck = abs(($nums[0] + $nums[1]) - $nums[2]) < 0.6;
                if ($sumCheck) {
                    $jn = $nums[0]; $on = $nums[1]; $reyting = $nums[2];
                    $yn = $nums[3]; $umumiy = $nums[4];
                } elseif ($nums[4] >= 0 && $nums[4] <= 100) {
                    $jn = $nums[0]; $on = 0; $reyting = $nums[1];
                    $yn = $nums[2]; $umumiy = $nums[3]; $davomat = $nums[4];
                } else {
                    $jn = $nums[0]; $on = $nums[1]; $reyting = $nums[2];
                    $yn = $nums[3]; $umumiy = $nums[4];
                }
            } elseif ($count === 4) {
                $jn = $nums[0]; $on = 0; $reyting = $nums[1];
                $yn = $nums[2]; $umumiy = $nums[3];
            } else {
                continue;
            }

            if ($reyting == 0.0 && ($jn > 0 || $on > 0)) {
                $reyting = $jn + $on;
            }
            if ($umumiy == 0.0 && ($reyting > 0 || $yn > 0)) {
                $umumiy = $reyting + $yn;
            }

            $rows[] = [
                'row_num'        => $rowNum,
                'name'           => $name,
                'reyting_raqami' => '',
                'jn'             => $jn,
                'on'             => $on,
                'reyting'        => $reyting,
                'yn'             => $yn,
                'umumiy'         => $umumiy,
                'baho'           => $umumiy,
                'davomat'        => $davomat,
            ];
        }

        return $rows;
    }

    private function parseBaholashQaydnomasi(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $n     = count($lines);
        $rows  = [];
        $pendingNameParts = [];
        $pendingRowNum    = null;

        for ($i = 0; $i < $n; $i++) {
            $line = trim($lines[$i]);
            if ($line === '') continue;

            if (preg_match(self::BAHOLASH_DATA_PATTERN, $line, $m)) {
                $reytingRaqami = $m[1];
                $jn = (float)$m[2]; $on = (float)$m[3]; $reyting = (float)$m[4];
                $yn = (float)$m[5]; $umumiy = (float)$m[6]; $baho = (float)$m[7];

                $idPos  = strpos($line, $reytingRaqami);
                $prefix = $idPos !== false ? trim(substr($line, 0, $idPos)) : '';
                $inlineName = $prefix;

                if (preg_match('/^(\d{1,3})\s*(.*)$/u', $prefix, $pm)) {
                    if ($pendingRowNum === null) $pendingRowNum = $pm[1];
                    $inlineName = trim($pm[2]);
                }
                if ($inlineName !== '') $pendingNameParts[] = $inlineName;

                if ($i + 1 < $n) {
                    $next = trim($lines[$i + 1]);
                    if ($next !== ''
                        && !preg_match(self::BAHOLASH_DATA_PATTERN, $next)
                        && !preg_match(self::IGNORE_PATTERN, $next)
                        && !preg_match('/^\d{1,3}$/u', $next)
                        && !$this->looksLikeNewName($next, $pendingNameParts)
                    ) {
                        $pendingNameParts[] = $next;
                        $i++;
                    }
                }

                $fullName = $this->normalizeName($this->joinFragments($pendingNameParts));

                if ($fullName !== '' && mb_strlen($fullName) >= 3) {
                    $rows[] = [
                        'row_num' => $pendingRowNum,
                        'name' => $fullName,
                        'reyting_raqami' => $reytingRaqami,
                        'jn' => $jn, 'on' => $on, 'reyting' => $reyting,
                        'yn' => $yn, 'umumiy' => $umumiy, 'baho' => $baho,
                        'davomat' => 0.0,
                    ];
                }

                $pendingNameParts = [];
                $pendingRowNum = null;
                continue;
            }

            if (preg_match(self::IGNORE_PATTERN, $line)) continue;
            if (preg_match('/^\d{1,3}$/u', $line)) { $pendingRowNum = $line; continue; }
            $pendingNameParts[] = $line;
        }

        return $rows;
    }

    private function looksLikeNewName(string $line, array $alreadyHaveParts): bool
    {
        if (preg_match('/^(O[‘\'`ʼ]?G[‘\'`ʼ]?LI|QIZI)$/ui', $line)) return false;
        if (empty($alreadyHaveParts)) return true;
        if (!preg_match('/\s/u', $line)) return false;
        if (preg_match('/^\S+\s+(O[‘\'`ʼ]?G[‘\'`ʼ]?LI|QIZI)$/ui', $line)) return false;
        return true;
    }

    private function joinFragments(array $fragments): string
    {
        $result = '';
        foreach (array_filter($fragments, fn($f) => trim((string)$f) !== '') as $fragment) {
            $fragment = trim((string)$fragment);
            if ($result !== '' && substr($result, -1) === '-') {
                $result = substr($result, 0, -1) . '-' . $fragment;
            } else {
                $result = trim($result . ' ' . $fragment);
            }
        }
        return $result;
    }

    private function normalizeName(string $name): string
    {
        $name = preg_replace('/^\d{1,3}\s+/u', '', $name);
        $name = preg_replace('/^(JN|ON|JN\+ON|imzosi|ko[‘\'`ʼ]?rsatkichi|o[‘\'`ʼ]?qituvchi)\s+/ui', '', $name);
        $name = preg_replace('/\s+/u', ' ', $name);
        return trim($name);
    }

    private function extractLayoutText(string $pdfPath): string
    {
        $process = new Process(['pdftotext', '-layout', $pdfPath, '-']);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                "PDF faylni o'qib bo'lmadi. Serverda 'poppler-utils' o'rnatilganini tekshiring. Xatolik: " . $process->getErrorOutput()
            );
        }
        return $process->getOutput();
    }
}