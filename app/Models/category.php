<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $fillable = [
        'nomi',
        'guruh',
    ];

    public function subjects()
    {
        return $this->hasMany(subject::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }

    public function elons(){
        return $this->hasMany(Elon::class);
    }
}
