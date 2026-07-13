<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class theme extends Model
{

    public function mini_semestr()
    {
        return $this->belongsTo(mini_semestr::class);
    }

}
