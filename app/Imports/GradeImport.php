<?php

namespace App\Imports;

use App\Models\grade; 
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;

class GradeImport implements ToModel
{
    private $subject_id;

    public function __construct($subject_id)
    {
        $this->subject_id = $subject_id;
    }

    public function model(array $row)
    {
        // Sarlavha qatorini tashlab ketish
        if ($row[1] === 'Talaba' || $row[1] === 'talaba') {
            return null;
        }

        $talabaIsmi = isset($row[1]) ? trim($row[1]) : null;
        $talabaGuruh = isset($row[2]) ? trim($row[2]) : null;

        if (!$talabaIsmi || !$talabaGuruh) {
            return null; 
        }

        $user = User::where('To‘liq_ismi', $talabaIsmi)
                    ->where('Guruh', $talabaGuruh)
                    ->first();

        if (!$user) {
            Log::warning("Talaba bazadan topilmadi: Ism: '{$talabaIsmi}' - Guruh: '{$talabaGuruh}'");
            return null; 
        }

        // --- MATNLI BAXOLARNI TOZALASH (MUAMMONING YECHIMI) ---
        // Agar Exceldan raqam emas, matn kelsa (masalan 'Baho qo‘yilmagan'), uni 0 ga aylantiramiz
        $joriy = is_numeric($row[3]) ? $row[3] : 0;
        $oraliq = is_numeric($row[4]) ? $row[4] : 0;
        $reyting = is_numeric($row[5]) ? $row[5] : 0;
        $yakuniy = is_numeric($row[6]) ? $row[6] : 0;
        $umumiy = is_numeric($row[8]) ? $row[8] : 0;
        $davomat = is_numeric($row[9]) ? $row[9] : 0;

        return new grade([
            'user_id'       => $user->id,
            'subject_id'    => $this->subject_id,
            'joriy_baho'    => $joriy,
            'oraliq_baho'   => $oraliq,
            'joriy_oraliq'  => $reyting,
            'yakuniy_baho'  => $yakuniy, // Endi bu yerga 'Baho qo‘yilmagan' emas, 0 boradi
            'umumiy'        => $umumiy,  // Bu yerga ham 0 boradi
            'davomat'       => $davomat, 
        ]);
    }
}
