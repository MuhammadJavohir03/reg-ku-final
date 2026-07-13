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
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('nomi');
            $table->foreignId('bolim_id')->nullable()->constrained('bolims')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('cascade');
            $table->enum('tur', ['free', 'mini'])->nullable();
            $table->integer('savollar_soni')->default(20);
            $table->integer('vaqt_limit')->nullable();
            $table->integer('urinish')->nullable();
            $table->dateTime('boshlanish_vaqti')->nullable();
            $table->dateTime('tugash_vaqti')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
};
