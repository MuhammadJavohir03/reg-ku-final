<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $fillable = [
        'nomi',
        'bolim_id',
        'subject_id',
        'topic_id',
        'tur',
        'savollar_soni',
        'vaqt_limit',
        'urinish',
        'boshlanish_vaqti', 
        'tugash_vaqti',     
    ];

    public function bolim()
    {
        return $this->belongsTo(Bolim::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'bank_id');
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class, 'bank_id');
    }
}
