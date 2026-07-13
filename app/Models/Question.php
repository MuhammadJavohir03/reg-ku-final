<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'bank_id', 'savol', 'togri_javob',
        'variant_1', 'variant_2', 'variant_3', 'variant_4', 'variant_5', 'ball',
    ];

    public function bank()
    {
        return $this->belongsTo(QuestionBank::class, 'bank_id');
    }

    public function questionUsers()
    {
        return $this->hasMany(QuestionUser::class, 'question_id');
    }
}