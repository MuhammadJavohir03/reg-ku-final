<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subject extends Model
{
    protected $fillable = [
        'nomi',
        'teacher_id',
        'category_id',
        'kafedra_id',
        'oquv_yili_id',
        'talim_tili',
        'fakultet_id',
        'kredit',
        'lesson_type_id',
        'semster',
    ];

    public function category()
    {
        return $this->belongsTo(category::class);
    }

    public function kafedra()
    {
        return $this->belongsTo(kafedra::class);
    }

    public function fakultet()
    {
        return $this->belongsTo(fakultet::class);
    }

    public function oquv_yili()
    {
        return $this->belongsTo(OquvYili::class, 'oquv_yili_id');
    }

    public function questionBanks()
    {
        return $this->hasMany(QuestionBank::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lesson_type()
    {
        return $this->belongsTo(lesson_type::class);
    }


    public function grade()
    {
        return $this->belongsTo(grade::class);
    }

    public function grades()
    {
        return $this->hasMany(grade::class, 'subject_id');
    }

    public function free_semestr()
    {
        return $this->belongsTo(free_semestr::class);
    }

    public function mini_semestr()
    {
        return $this->belongsTo(mini_semestr::class);
    }

    public function miniSemestrs()
    {
        return $this->hasMany(mini_semestr::class, 'subject_id', 'id');
    }

    public function freeSemestrs()
    {
        return $this->hasMany(free_semestr::class, 'subject_id', 'id');
    }
}
