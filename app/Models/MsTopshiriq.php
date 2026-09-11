<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MsTopshiriq extends Model
{
    protected $table = 'ms_topshiriqlar';

    protected $fillable = [
        'ms_material_id',
        'user_id',
        'pdf_path',
        'ball',
    ];

    protected $casts = [
        'ball' => 'float',
    ];

    public function material()
    {
        return $this->belongsTo(MsMaterial::class, 'ms_material_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function pdfUrl(): ?string
    {
        if (! $this->pdf_path) {
            return null;
        }

        return Storage::disk('public')->url($this->pdf_path);
    }

    public function isTopshirilgan(): bool
    {
        return ! empty($this->pdf_path);
    }
}
