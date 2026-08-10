<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectsToSubjectTeacher extends Model
{
    protected $table = 'subjects_to_subject_teacher';

    protected $fillable = ['subjects_to_subject_id', 'teacher_id', 'max_talaba'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subjectsToSubject()
    {
        return $this->belongsTo(SubjectsToSubject::class, 'subjects_to_subject_id');
    }
}