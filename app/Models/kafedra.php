<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kafedra extends Model
{
    protected $table = 'kafedra';

    protected $fillable = [
        'nomi',
        'fakultet_id',
    ];

    public function fakultet()
    {
        return $this->belongsTo(fakultet::class);
    }

    public function subjects()
    {
        return $this->hasMany(subject::class, 'kafedra_id');
    }
}