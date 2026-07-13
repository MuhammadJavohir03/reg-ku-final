<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bolim extends Model
{
    protected $fillable = [
        'name',
    ];
    public function mini_semestrs()
    {
        return $this->hasMany(mini_semestr::class);
    }

    public function free_semestrs()
    {
        return $this->hasMany(free_semestr::class);
    }
}
