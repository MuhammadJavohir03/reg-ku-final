<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class fakultet extends Model
{
    protected $table = 'fakultet';

    protected $fillable = [
        'nomi',
    ];
    public function kafedralar()
    {
        return $this->hasMany(kafedra::class, 'fakultet_id');
    }
}
