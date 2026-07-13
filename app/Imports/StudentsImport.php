<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Str;

class StudentsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    private $password;

    public function __construct()
    {
        // 12,350 marta hash qilmaslik uchun parolni bir marta tayyorlab olamiz
        $this->password = Hash::make('reg1234567');
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $talabaId = $row['talaba_id'] ?? $row['id'] ?? null;

        if (!$talabaId) {
            return null; // ID bo'lmasa qatorni tashlab ketish
        }
        // 1. Guruh kalitini xavfsiz aniqlab olish
        $guruh = $row['guruh'] ?? $row['Guruh'] ?? $row['group'] ?? '';

        if (!empty($guruh)) {
            // Guruhdan yilni ajratib olish (XT-1-22 -> 22)
            $guruhYili = (int) substr(strrchr($guruh, "-"), 1);

            if ($guruhYili > 0) {
                $joriyYil = (int) date('y'); // 2026-yil bo'lsa, 26
                $hisoblanganKurs = $joriyYil - $guruhYili;

                // 2-sentabr mantiqi
                if (date('m-d') >= '09-02') {
                    $hisoblanganKurs++;
                }

                // Bitiruvchi mantiqi
                $isBitiruvchi = 'Yo‘q';
                if ($hisoblanganKurs > 4) {
                    $hisoblanganKurs = 4;
                    $isBitiruvchi = 'Ha';
                }
            } else {
                $hisoblanganKurs = $row['kurs'] ?? $row['Kurs'] ?? 1;
                $isBitiruvchi = $row['bitiruvchi'] ?? $row['Bitiruvchi'] ?? 'Yo‘q';
            }
        } else {
            // Agar guruh ustuni Excelda umuman topilmasa
            $hisoblanganKurs = $row['kurs'] ?? $row['Kurs'] ?? 1;
            $isBitiruvchi = 'Yo‘q';
        }

        return new User([
            // Avtomatik login ma'lumotlari
            'email'    => $talabaId . '@reg.uz',
            'password' => $this->password,
            'role' => 'talaba',

            // Excel ustunlari (Kalitlarni Excel sarlavhasiga qarab moslang)
            'Talaba_ID'                    => $talabaId,
            'To‘liq_ismi'                  => $row['to_liq_ismi'] ?? $row['toliq_ismi'] ?? $row['fio'] ?? null,
            'Fuqarolik'                    => $row['fuqarolik'] ?? null,
            'Davlat'                       => $row['davlat'] ?? null,
            'Millat'                       => $row['millat'] ?? null,
            'Viloyat'                      => $row['viloyat'] ?? null,
            'Tuman'                        => $row['tuman'] ?? null,
            'Jins'                         => $row['jins'] ?? null,
            'Tug‘ilgan_sana'               => $row['tug_ilgan_sana'] ?? $row['tugilgan_sana'] ?? null,
            'Pasport_raqami'               => $row['pasport_raqami'] ?? null,
            'JSHSHIR_kod'                  => $row['jshshir_kod'] ?? $row['jshshir'] ?? null,
            'Pasport_berilgan_sana'        => $row['pasport_berilgan_sana'] ?? null,
            'Kurs'       => $hisoblanganKurs,
            'Fakultet'                     => $row['fakultet'] ?? null,
            'Guruh'                        => $row['guruh'] ?? null,
            'Ta_lim_tili'                  => $row['ta_lim_tili'] ?? $row['talim_tili'] ?? null,
            'O‘quv_yili'                   => $row['o_quv_yili'] ?? $row['oquv_yili'] ?? null,
            'Semestr'                      => $row['semestr'] ?? null,
            'Bitiruvchi'                   => $isBitiruvchi,
            'Mutaxassislik'                => $row['mutaxassislik'] ?? null,
            'Ta’lim_turi'                  => $row['ta_lim_turi'] ?? $row['talim_turi'] ?? null,
            'Ta’lim_shakli'                => $row['ta_lim_shakli'] ?? $row['talim_shakli'] ?? null,
            'To‘lov_shakli'                => $row['to_lov_shakli'] ?? $row['tolov_shakli'] ?? null,
            'Grant_turi'                   => $row['grant_turi'] ?? null,
            'Avvalgi_ta_lim_ma_lumoti'     => $row['avvalgi_ta_lim_ma_lumoti'] ?? null,
            'Talaba_toifasi'               => $row['talaba_toifasi'] ?? null,
            'Ijtimoiy_toifa'               => $row['ijtimoiy_toifa'] ?? null,
            'Birga_yashaydiganlar_soni'    => $row['birga_yashaydiganlar_soni'] ?? 0,
            'Birga_yashaydiganlar_toifasi' => $row['birga_yashaydiganlar_toifasi'] ?? null,
            'Yashash_joyi_statusi'         => $row['yashash_joyi_statusi'] ?? null,
            'Yashash_joyi_geolokatsiyasi'  => $row['yashash_joyi_geolokatsiyasi'] ?? null,
            'Buyruq'                       => $row['buyruq'] ?? null,
            'GPA'                          => $row['gpa'] ?? 0,
            'Kontrakt_N'                   => $row['kontrakt_n'] ?? null,
            'Shartnoma_turi'               => $row['shartnoma_turi'] ?? null,
        ]);
    }

    /**
     * Har bir SQL so'rovda nechta qator bazaga yozilishi
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * Faylni nechta qatordan bo'lib o'qish (RAMni tejash)
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
