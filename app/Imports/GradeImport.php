<?php

namespace App\Imports;

use App\Models\grade; 
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;

class GradeImport implements ToModel
{
    private $subject_id;

    // Statistika uchun sanoqchilar
    public $yangiQoshildi = 0;   // Yangi baho qo'shildi
    public $yangilandi = 0;      // Mavjud baho yangilandi (takrorlanish)
    public $talabaTopilmadi = 0; // Baza bo'yicha talaba topilmagan qatorlar

    public function __construct($subject_id)
    {
        $this->subject_id = $subject_id;
    }

    public function model(array $row)
    {
        // Sarlavha qatorini tashlab ketish
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

        // --- MATNLI BAXOLARNI TOZALASH ---
        $joriy   = is_numeric($row[2]) ? $row[2] : 0; // Joriy nazorat
        $oraliq  = is_numeric($row[3]) ? $row[3] : 0; // Oraliq nazorat
        $reyting = is_numeric($row[4]) ? $row[4] : 0; // Reyting
        $yakuniy = is_numeric($row[5]) ? $row[5] : 0; // Yakuniy nazorat
        $umumiy  = is_numeric($row[7]) ? $row[7] : 0; // Umumiy
        $davomat = is_numeric($row[8]) ? $row[8] : 0; // Davomat %

        // --- QO'SHIMCHA: agar umumiy 0 bo'lib qolgan bo'lsa ---
        // (tizim 50/60 dan past bahoni 0 qilib yuborgani uchun),
        // joriy + oraliq + yakuniy yig'indisini umumiy sifatida olamiz
        if ((float) $umumiy == 0) {
            $umumiy = $joriy + $oraliq + $yakuniy;
        }

        // --- TAKRORLANISHNI OLDINI OLISH ---
        // Bitta talaba (user_id) + bitta fan (subject_id) uchun faqat bitta yozuv bo'lishi kerak.
        // Agar avval yuklangan bo'lsa - yangilaymiz, yo'q bo'lsa - yangi qo'shamiz.
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

        // Saqlashni o'zimiz updateOrCreate orqali qilganimiz uchun
        // ToModel'ga qayta insert qildirmaslik uchun null qaytaramiz.
        return null;
    }
}