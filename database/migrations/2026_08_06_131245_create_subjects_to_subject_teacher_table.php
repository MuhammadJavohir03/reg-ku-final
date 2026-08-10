<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects_to_subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subjects_to_subject_id')
                ->constrained('subjects_to_subject')
                ->cascadeOnDelete();
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedInteger('max_talaba')->default(30);
            $table->timestamps();

            $table->unique(['subjects_to_subject_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects_to_subject_teacher');
    }
};