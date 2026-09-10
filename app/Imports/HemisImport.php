<?php

namespace App\Imports;

use App\Models\grade;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class HemisImport
{
    private $subject_id;
    public $yangiQoshildi = 0;
    public $yangilandi = 0;
    public $talabaTopilmadi = 0;

    public function __construct($subject_id)
    {
        $this->subject_id = $subject_id;
    }

    public function processRows(array $rows): void
    {
        foreach ($rows as $row) {
            $this->processRow($row);
        }
    }

    private function processRow(array $row): void
    {
        $talabaIsmi    = trim((string)($row['name'] ?? ''));
        $reytingRaqami = trim((string)($row['reyting_raqami'] ?? ''));
        if ($talabaIsmi === '' && $reytingRaqami === '') return;

        $user = $this->talabaniTop($talabaIsmi, $reytingRaqami);
        if (!$user) {
            Log::warning("[Hemis import] Talaba topilmadi: Ism: '{$talabaIsmi}' - Reyting raqami: '{$reytingRaqami}'");
            $this->talabaTopilmadi++;
            return;
        }

        $jn = (float)($row['jn'] ?? 0);
        $on = (float)($row['on'] ?? 0);
        $reyting = isset($row['reyting']) ? (float)$row['reyting'] : ($jn + $on);
        $yn = (float)($row['yn'] ?? 0);
        $umumiy = isset($row['umumiy']) ? (float)$row['umumiy'] : ($reyting + $yn);
        $davomat = (float) ($row['davomat'] ?? 0);

        $mavjud = grade::where('user_id', $user->id)->where('subject_id', $this->subject_id)->first();
        $mavjud ? $this->yangilandi++ : $this->yangiQoshildi++;

        grade::updateOrCreate(
            ['user_id' => $user->id, 'subject_id' => $this->subject_id],
            [
                'joriy_baho' => $jn,
                'oraliq_baho' => $on,
                'joriy_oraliq' => $reyting,
                'yakuniy_baho' => $yn,
                'umumiy' => $umumiy,
                'davomat'      => $davomat, 
            ]
        );
    }

    private function talabaniTop(?string $talabaIsmi, ?string $reytingRaqami): ?User
    {
        if ($reytingRaqami !== '' && Schema::hasColumn('users', 'reyting_raqami')) {
            $user = User::where('reyting_raqami', $reytingRaqami)->first();
            if ($user) return $user;
        }
        if (!$talabaIsmi) return null;

        $user = User::where('To‘liq_ismi', $talabaIsmi)->first();
        if ($user) return $user;

        $normalized = $this->normalize($talabaIsmi);
        $familiya = $this->extractFamiliya($normalized);
        if ($familiya === '') return null;

        $candidates = User::where('To‘liq_ismi', 'like', $familiya . '%')->limit(50)->get();
        if ($candidates->isEmpty()) {
            $candidates = User::whereRaw('LOWER(`To‘liq_ismi`) LIKE ?', [mb_strtolower($familiya) . '%'])->limit(50)->get();
        }

        foreach ($candidates as $candidate) {
            $candNorm = $this->normalize($candidate->{'To‘liq_ismi'});
            if ($candNorm === $normalized) return $candidate;

            $shortPdf  = $this->stripSuffix($normalized);
            $shortCand = $this->stripSuffix($candNorm);
            if ($shortPdf !== '' && $shortPdf === $shortCand) return $candidate;
        }

        $pdfParts = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        if (count($pdfParts) >= 2) {
            $pdfKey = $pdfParts[0] . ' ' . $pdfParts[1];
            foreach ($candidates as $candidate) {
                $candParts = preg_split('/\s+/u', $this->normalize($candidate->{'To‘liq_ismi'}), -1, PREG_SPLIT_NO_EMPTY);
                if (count($candParts) >= 2 && ($candParts[0] . ' ' . $candParts[1]) === $pdfKey) {
                    return $candidate;
                }
            }
        }
        return null;
    }

    private function normalize(string $text): string
    {
        $text = str_replace(["‘", "’", "`", "ʼ", "ʻ", "´", "′"], "'", $text);
        $text = preg_replace('/\s*-\s*/u', '-', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim(mb_strtoupper($text, 'UTF-8'));
    }

    private function stripSuffix(string $normalized): string
    {
        $result = preg_replace('/[\s\-]+O\'?G\'?LI$/u', '', $normalized);
        $result = preg_replace('/[\s\-]+QIZI$/u', '', $result);
        return trim($result);
    }

    private function extractFamiliya(string $normalized): string
    {
        $parts = preg_split('/\s+/u', $normalized, 2, PREG_SPLIT_NO_EMPTY);
        return $parts[0] ?? '';
    }
}