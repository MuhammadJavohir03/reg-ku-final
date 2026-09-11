<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    protected $fillable = [
        'bank_id',
        'ms_material_id',
        'user_id',
        'savol_soni',
        'boshlanish_vaqti',
        'tugash_vaqti',
        'boshlanish_sanasi',
        'tugash_sanasi',
        'ball',
        'status'
    ];

    protected $casts = [
        'boshlanish_vaqti'  => 'datetime',
        'tugash_vaqti'      => 'datetime',
        'boshlanish_sanasi' => 'date',
        'tugash_sanasi'     => 'date',
    ];

    public function bank()
    {
        return $this->belongsTo(QuestionBank::class, 'bank_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questionUsers()
    {
        return $this->hasMany(QuestionUser::class, 'session_id');
    }

    public function isExpired(): bool
    {
        return now()->gt($this->tugash_vaqti);
    }

    public function msMaterial()
    {
        return $this->belongsTo(MsMaterial::class, 'ms_material_id');
    }


    public function material()
    {
        return $this->belongsTo(MsMaterial::class, 'ms_material_id');
    }

    public function hisoblaBall(): int
    {
        return $this->questionUsers()->where('status', 1)->count();
    }
}