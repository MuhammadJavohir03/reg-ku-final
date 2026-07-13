<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MsJoriyBaho extends Model
{
    protected $table = 'ms_joriy_baho';

    protected $fillable = [
        'user_id',
        'mavzu_id',
        'baho',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mavzu(): BelongsTo
    {
        return $this->belongsTo(MsMavzu::class, 'mavzu_id');
    }
}
