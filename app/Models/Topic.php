<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'bolim_id',
        'subject_id',
        'nomi',
        'tartib_raqami',
        'video_url',
        'video_file',
        'pdf_file',
        'max_ball',
        'test_bor',
    ];

    protected $casts = [
        'test_bor' => 'boolean',
    ];

    public function bolim()
    {
        return $this->belongsTo(Bolim::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function testSetting()
    {
        return $this->hasOne(TestSetting::class);
    }

    public function mini_semestr()
    {
        return $this->belongsTo(mini_semestr::class);
    }
}
