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