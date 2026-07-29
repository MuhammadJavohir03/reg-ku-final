<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OquvYili extends Model
{
    protected $table = 'oquv_yili'; // O'quv yili jadvali nomi
    protected $fillable = ['nomi']; // Mass assignment uchun ruxsat berilgan maydonlar

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'oquv_yili_id'); // O'quv yili bilan bog'liq fanlar
    }
}
