<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BepulSemestr extends Model
{
    protected $table = 'bolims';
    protected $fillable = [
        'nomi',
        'status'
    ];

    protected static function booted()
    {

        static::saving(function ($model) {

            if ($model->status == 1) {


                static::where('id', '!=', $model->id)->update(['status' => 0]);
            }
        });
    }
}
