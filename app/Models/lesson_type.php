<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lesson_type extends Model
{
    protected $fillable = [
        'nomi',
    ];
    public function subjects()
    {
        return $this->hasMany(subject::class);
    }
}
