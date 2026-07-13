<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bolim_id')->constrained('bolims')->onDelete('cascade');
            $table->foreignId('mini_semestr_id')->nullable()->constrained('mini_semestrs')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('nomi');
            $table->integer('tartib_raqami');
            $table->string('video_file')->nullable();
            $table->string('pdf_file')->nullable();
            $table->integer('max_ball')->default(10);
            $table->boolean('test_bor')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
