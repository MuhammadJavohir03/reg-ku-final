<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'section_user')->withTimestamps();
    }

    
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

}
