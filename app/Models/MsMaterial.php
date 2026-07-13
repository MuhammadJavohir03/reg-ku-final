<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MsMaterial extends Model
{
    protected $table = 'ms_materiallar';

    protected $fillable = [
        'mavzu_id',
        'tur', // test | video | pdf
        'nomi',
        'bank_id',
        'savollar_soni',
        'vaqt_limit',
        'urinish',
        'boshlanish_vaqti',
        'tugash_vaqti',
        'video_path',
        'video_size',
        'video_mime',
        'pdf_path',
        'pdf_size',
        'pdf_sahifalar',
        'tartib',
        'faol',
    ];

    protected $casts = [
        'faol'             => 'boolean',
        'boshlanish_vaqti' => 'datetime',
        'tugash_vaqti'     => 'datetime',
    ];

    public function mavzu(): BelongsTo
    {
        return $this->belongsTo(MsMavzu::class, 'mavzu_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'bank_id');
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class, 'ms_material_id');
    }

    public function videoUrl(): ?string
    {
        if (!$this->video_path) {
            return null;
        }

        // DB'da "storage/..." bilan boshlanib saqlangan bo'lsa, qayta qo'shmaymiz
        $path = ltrim($this->video_path, '/');
        $path = str_starts_with($path, 'storage/') ? substr($path, 8) : $path;

        return asset('storage/' . $path);
    }

    public function pdfUrl(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }

        $path = ltrim($this->pdf_path, '/');
        $path = str_starts_with($path, 'storage/') ? substr($path, 8) : $path;

        return asset('storage/' . $path);
    }
}
