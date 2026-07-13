<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Elon extends Model
{
    protected $fillable = [
        'admin_id',
        'title',
        'short_content',
        'full_content',
        'photo',
        'category_id',
        'kurs',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function category(){
        return $this->belongsTo(category::class, 'category_id');
    }
}
    