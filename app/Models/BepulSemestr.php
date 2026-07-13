<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BepulSemestr extends Model
{
    protected $table = 'bolims';
    protected $fillable = [
        'nomi',
        'status'
    ];

    protected static function booted()
    {
        // saving — bu ham store (yaratish), ham update (tahrirlash) paytida ishlaydi
        static::saving(function ($model) {
            // Agar saqlanayotgan bo'lim statusi 1 (Active) bo'lsa
            if ($model->status == 1) {
                // O'zidan boshqa barcha bo'limlarni 0 (Block) qilamiz
                // static:: — bu modelning o'ziga murojaat
                static::where('id', '!=', $model->id)->update(['status' => 0]);
            }
        });
    }
}
