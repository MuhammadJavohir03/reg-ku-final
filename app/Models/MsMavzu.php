<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsMavzu extends Model
{
    protected $table = 'ms_mavzular';

    protected $fillable = [
        'bolim_id',
        'subject_id',
        'nomi',
        'tur',
        'tartib',
        'faol',
    ];

    protected $casts = [
        'faol' => 'boolean',
    ];

    // Tur yorliqlari
    public function turNomi(): string
    {
        return match($this->tur) {
            'mavzu'   => 'Mavzu',
            'oraliq'  => 'Oraliq',
            'yakuniy' => 'Yakuniy',
            default   => ucfirst($this->tur),
        };
    }

    // Tur badge rangi
    public function turRangi(): string
    {
        return match($this->tur) {
            'mavzu'   => '#EEEDFE',
            'oraliq'  => '#fff3cd',
            'yakuniy' => '#d1fae5',
            default   => '#f0f0f0',
        };
    }

    public function bolim(): BelongsTo
    {
        return $this->belongsTo(Bolim::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materiallar(): HasMany
    {
        return $this->hasMany(MsMaterial::class, 'mavzu_id')->orderBy('tartib');
    }
}