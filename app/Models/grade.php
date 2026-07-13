<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class grade extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'joriy_baho',
        'oraliq_baho',
        'joriy_oraliq',
        'yakuniy_baho',
        'umumiy',
        'davomat',
    ];

    public function subject()
    {
        return $this->belongsTo(subject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
