<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('kafedra_id')->constrained('kafedra')->onDelete('cascade');
            $table->foreignId('fakultet_id')->constrained('fakultet')->onDelete('cascade');
            $table->integer('kredit')->nullable();
            $table->foreignId('oquv_yili_id')->nullable()->constrained('oquv_yili')->onDelete('cascade');
            $table->string('talim_tili')->nullable();

            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('lesson_type_id')->nullable();
            $table->foreign('lesson_type_id')->references('id')->on('lesson_types')->onDelete('cascade');

            $table->string('semster');

            $table->string('nomi');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
