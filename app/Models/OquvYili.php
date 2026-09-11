<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OquvYili extends Model
{
    protected $table = 'oquv_yili';
    protected $fillable = ['nomi'];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'oquv_yili_id');
    }
}
