<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mudir extends Model
{
    protected $table = 'mudirlar';

    protected $fillable = [
        'mudir',
        'oquv_yili_id',
        'kafedra_id',
    ];

    public function oquvYili()
    {
        return $this->belongsTo(OquvYili::class, 'oquv_yili_id');
    }

    public function kafedra()
    {
        return $this->belongsTo(kafedra::class, 'kafedra_id');
    }

    /**
     * To'liq ism-familiyani ("Baxtiyorjonov Muhammadjavohir Jamshidjon o'g'li")
     * imzo uchun qisqa ko'rinishga ("M.Baxtiyorjonov") aylantiradi. Bazada har doim
     * to'liq ism saqlanadi - bu format faqat ko'rinish/eksport uchun hisoblanadi.
     *
     * Kiritish tartibi: Familiya Ism [Otasining ismi] (standart rasmiy tartib).
     * Qoida: birinchi so'z - familiya (to'liq, o'zgarishsiz), ikkinchi so'z - ism
     * (faqat bosh harfi olinadi), qolgan so'z(lar) (otasining ismi) e'tiborga olinmaydi.
     * Agar faqat bitta so'z kiritilgan bo'lsa (masalan, allaqachon
     * "M.Baxtiyorjonov" ko'rinishida yozilgan bo'lsa), o'sha so'z o'zgarishsiz qaytariladi.
     */
    public static function formatSignature(?string $fullName): string
    {
        $fullName = trim((string) $fullName);

        if ($fullName === '') {
            return '';
        }

        $parts = preg_split('/\s+/u', $fullName);

        if (count($parts) < 2) {
            return $fullName;
        }

        $familiya = $parts[0];
        $ism = $parts[1];

        $birinchiHarf = mb_strtoupper(mb_substr($ism, 0, 1, 'UTF-8'), 'UTF-8');

        return $birinchiHarf . '.' . $familiya;
    }
}