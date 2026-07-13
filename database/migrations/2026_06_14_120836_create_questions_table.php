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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('question_banks')->onDelete('cascade');
            $table->text('savol');
            $table->integer('ball')->nullable();
            $table->string('togri_javob');
            $table->string('variant_1');
            $table->string('variant_2');
            $table->string('variant_3')->nullable();
            $table->string('variant_4')->nullable();
            $table->string('variant_5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
