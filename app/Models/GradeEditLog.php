<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeEditLog extends Model
{
    protected $fillable = [
        'editor_id',
        'editable_type',
        'record_id',
        'field',
        'student_id',
        'mavzu_id',
        'old_value',
        'new_value',
        'ip_address',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}