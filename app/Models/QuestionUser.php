<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionUser extends Model
{
    protected $table = 'question_user';

    protected $fillable = [
        'session_id',
        'question_id',
        'tanlov',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(TestSession::class, 'session_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
