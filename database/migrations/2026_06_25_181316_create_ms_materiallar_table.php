<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_materiallar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mavzu_id')->constrained('ms_mavzular')->onDelete('cascade');

            $table->enum('tur', ['test', 'video', 'pdf']);
            $table->string('nomi');

            // TEST uchun
            $table->foreignId('bank_id')->nullable()->constrained('question_banks')->nullOnDelete();
            $table->integer('savollar_soni')->nullable();
            $table->integer('vaqt_limit')->nullable();   // daqiqa
            $table->integer('urinish')->nullable();
            $table->timestamp('boshlanish_vaqti')->nullable();
            $table->timestamp('tugash_vaqti')->nullable();

            // VIDEO uchun (storage path)
            $table->string('video_path')->nullable();    // storage/app/public/ms_videos/...
            $table->string('video_size')->nullable();    // MB ko'rinishida
            $table->string('video_mime')->nullable();    // video/mp4 ...

            // PDF uchun (storage path)
            $table->string('pdf_path')->nullable();      // storage/app/public/ms_pdfs/...
            $table->string('pdf_size')->nullable();
            $table->string('pdf_sahifalar')->nullable(); // sahifalar soni

            $table->integer('tartib')->default(0);
            $table->boolean('faol')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_materiallar');
    }
};