<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mini_semestr extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'bolim_id',
        'joriy_baho',
        'oraliq_baho',
        'joriy_oraliq',
        'yakuniy_baho',
        'umumiy',
        'status'

    ];
    
    public function subject()
    {
        return $this->belongsTo(subject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bolim()
    {
        return $this->belongsTo(bolim::class);
    }

}
