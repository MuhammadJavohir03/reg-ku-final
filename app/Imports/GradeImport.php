<?php

namespace App\Imports;

use App\Models\grade; 
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;

class GradeImport implements ToModel
{
    private $subject_id;

    public $yangiQoshildi = 0;
    public $yangilandi = 0;
    public $talabaTopilmadi = 0;

    public function __construct($subject_id)
    {
        $this->subject_id = $subject_id;
    }

    public function model(array $row)
    {

        if ($row[0] === 'Talaba' || $row[0] === 'talaba') {
            return null;
        }

        $talabaIsmi = isset($row[0]) ? trim($row[0]) : null;
        $talabaGuruh = isset($row[1]) ? trim($row[1]) : null;

        if (!$talabaIsmi || !$talabaGuruh) {
            return null; 
        }

        $user = User::where('To‘liq_ismi', $talabaIsmi)
                    ->where('Guruh', $talabaGuruh)
                    ->first();

        if (!$user) {
            Log::warning("Talaba bazadan topilmadi: Ism: '{$talabaIsmi}' - Guruh: '{$talabaGuruh}'");
            $this->talabaTopilmadi++;
            return null; 
        }

        $joriy   = is_numeric($row[2]) ? $row[2] : 0;
        $oraliq  = is_numeric($row[3]) ? $row[3] : 0;
        $reyting = is_numeric($row[4]) ? $row[4] : 0;
        $yakuniy = is_numeric($row[5]) ? $row[5] : 0;
        $umumiy  = is_numeric($row[7]) ? $row[7] : 0;
        $davomat = is_numeric($row[8]) ? $row[8] : 0;



        if ((float) $umumiy == 0) {
            $umumiy = $joriy + $oraliq + $yakuniy;
        }



        $mavjudBaho = grade::where('user_id', $user->id)
                            ->where('subject_id', $this->subject_id)
                            ->first();

        if ($mavjudBaho) {
            $this->yangilandi++;
        } else {
            $this->yangiQoshildi++;
        }

        grade::updateOrCreate(
            [
                'user_id'    => $user->id,
                'subject_id' => $this->subject_id,
            ],
            [
                'joriy_baho'   => $joriy,
                'oraliq_baho'  => $oraliq,
                'joriy_oraliq' => $reyting,
                'yakuniy_baho' => $yakuniy,
                'umumiy'       => $umumiy,
                'davomat'      => $davomat,
            ]
        );


        return null;
    }
}