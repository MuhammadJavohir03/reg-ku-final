<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectsToSubject extends Model
{
    protected $table = 'subjects_to_subject';

    protected $fillable = ['nomi'];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'subjects_to_subject_id');
    }

    public function teachers()
    {
        return $this->hasMany(SubjectsToSubjectTeacher::class, 'subjects_to_subject_id');
    }

    public function miniSemestrs()
    {
        return $this->hasMany(mini_semestr::class, 'subjects_to_subject_id');
    }
}