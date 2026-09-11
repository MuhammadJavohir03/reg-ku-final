<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    private $password;

    public function __construct()
    {

        $this->password = Hash::make('reg1234567');
    }

    
    public function model(array $row)
    {

        $row = $this->normalizeKeys($row);

        $talabaId = $this->val($row, [
            'id_raqam', 'id_raqami', 'talaba_id', 'id', 'talabaid'
        ]);

        if (!$talabaId) {
            return null;
        }

        $talabaId = trim((string) $talabaId);

        $guruh = $this->val($row, ['guruh', 'group']) ?? '';

        $hisoblanganKurs = 1;
        $isBitiruvchi = 'Yo‘q';

        if (!empty($guruh)) {
            $guruhYili = (int) substr(strrchr($guruh, '-'), 1);

            if ($guruhYili > 0) {
                $joriyYil = (int) date('y');
                $hisoblanganKurs = $joriyYil - $guruhYili;

                if (date('m-d') >= '09-02') {
                    $hisoblanganKurs++;
                }

                if ($hisoblanganKurs > 4) {
                    $hisoblanganKurs = 4;
                    $isBitiruvchi = 'Ha';
                } elseif ($hisoblanganKurs < 1) {
                    $hisoblanganKurs = 1;
                }
            }
        }

        if ($hisoblanganKurs === 1 || empty($guruh)) {
            $kursRaw = $this->val($row, ['kurs']);
            if ($kursRaw !== null && $kursRaw !== '') {
                if (preg_match('/(\d+)/', (string) $kursRaw, $m)) {
                    $hisoblanganKurs = (int) $m[1];
                }
            }
        }

        $harakat = mb_strtolower(trim((string) ($this->val($row, [
            'talaba_harakati', 'harakat', 'bitiruvchi', 'status'
        ]) ?? '')));

        if (in_array($harakat, ['bitirgan', 'bitirdi', 'ha'], true)) {
            $isBitiruvchi = 'Ha';
        } elseif (in_array($harakat, ['o‘qimoqda', 'oqimoqda', "o'qimoqda", 'yo‘q', "yo'q"], true)) {
            $isBitiruvchi = 'Yo‘q';
        }



        $semestrRaw = $this->val($row, ['semestr']);
        $semestr = null;
        if ($semestrRaw !== null && $semestrRaw !== '') {
            if (preg_match('/(\d+)/', (string) $semestrRaw, $m)) {
                $semestr = $m[1];
            } else {
                $semestr = $semestrRaw;
            }
        }

        $tugilgan = $this->val($row, [
            'tugilgan_sana', 'tug_ilgan_sana', 'tug‘ilgan_sana', 'birth_date'
        ]);
        if ($tugilgan && preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', trim((string) $tugilgan), $m)) {
            $tugilgan = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        $data = [
            'To‘liq_ismi'    => $this->cut($this->val($row, [
                'toliq_ismi', 'to_liq_ismi', 'to‘liq_ismi', 'fio', 'ism'
            ]), 255),
            'Pasport_raqami' => $this->cut($this->val($row, ['pasport_raqami', 'passport']), 20),
            'JSHSHIR_kod'    => $this->cut($this->val($row, [
                'jshshir_kod', 'jshshir-kod', 'jshshir', 'pinfl'
            ]), 14),
            'Tug‘ilgan_sana' => $tugilgan,
            'Jins'           => $this->cut($this->val($row, ['jins', 'gender']), 20),

            'Kurs'           => $hisoblanganKurs,
            'Fakultet'       => $this->cut($this->val($row, ['fakultet', 'faculty']), 255),
            'Guruh'          => $guruh !== '' ? $this->cut($guruh, 50) : null,
            'Mutaxassislik'  => $this->cut($this->val($row, ['mutaxassislik', 'specialty']), 255),

            'Ta’lim_turi'    => $this->cut($this->val($row, [
                'talim_turi', 'ta_lim_turi', 'ta’lim_turi'
            ]), 100),
            'Ta’lim_shakli'  => $this->cut($this->val($row, [
                'talim_shakli', 'ta_lim_shakli', 'ta’lim_shakli'
            ]), 100),
            'To‘lov_shakli'  => $this->cut($this->val($row, [
                'tolov_shakli', 'to_lov_shakli', 'to‘lov_shakli'
            ]), 100),
            'O‘quv_yili'     => $this->cut($this->val($row, [
                'oquv_yili', 'o_quv_yili', 'o‘quv_yili'
            ]), 50),
            'Semestr'        => $semestr,
            'Bitiruvchi'     => $isBitiruvchi,
        ];

        $existing = User::where('Talaba_ID', $talabaId)->first();

        if ($existing) {


            $updateData = array_filter($data, fn($v) => $v !== null && $v !== '');
            if (!empty($updateData)) {
                $existing->update($updateData);
            }
            return null;
        }

        return new User([
            'Talaba_ID'      => $talabaId,
            'email'          => $talabaId . '@reg.uz',
            'password'       => $this->password,
            'role'           => 'talaba',
            'GPA'            => 0,

            'To‘liq_ismi'    => $data['To‘liq_ismi'],
            'Pasport_raqami' => $data['Pasport_raqami'],
            'JSHSHIR_kod'    => $data['JSHSHIR_kod'],
            'Tug‘ilgan_sana' => $data['Tug‘ilgan_sana'],
            'Jins'           => $data['Jins'],
            'Kurs'           => $data['Kurs'],
            'Fakultet'       => $data['Fakultet'],
            'Guruh'          => $data['Guruh'],
            'Mutaxassislik'  => $data['Mutaxassislik'],
            'Ta’lim_turi'    => $data['Ta’lim_turi'],
            'Ta’lim_shakli'  => $data['Ta’lim_shakli'],
            'To‘lov_shakli'  => $data['To‘lov_shakli'],
            'O‘quv_yili'     => $data['O‘quv_yili'],
            'Semestr'        => $data['Semestr'],
            'Bitiruvchi'     => $data['Bitiruvchi'],
        ]);
    }

    
    private function normalizeKeys(array $row): array
    {
        $out = [];
        foreach ($row as $key => $value) {
            $k = mb_strtolower((string) $key);
            $k = str_replace(
                ["‘", "’", "ʻ", "ʼ", "`", "´", "'", "–", "—", "-"],
                ['', '', '', '', '', '', '', '_', '_', '_'],
                $k
            );
            $k = preg_replace('/[^a-z0-9_]+/u', '_', $k);
            $k = trim(preg_replace('/_+/', '_', $k), '_');
            $out[$k] = $value;

            $out[$key] = $value;
        }
        return $out;
    }

    
    private function val(array $row, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return is_string($row[$key]) ? trim($row[$key]) : $row[$key];
            }
        }
        return null;
    }

    
    private function cut($value, int $max): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = (string) $value;
        if (mb_strlen($value) <= $max) {
            return $value;
        }
        return mb_substr($value, 0, $max);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}