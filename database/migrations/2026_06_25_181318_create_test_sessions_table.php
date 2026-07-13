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
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('question_banks')->onDelete('cascade');
            $table->foreignId('ms_material_id')->nullable()->constrained('ms_materiallar')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('savol_soni')->default(20);
            $table->dateTime('boshlanish_vaqti')->nullable();
            $table->dateTime('tugash_vaqti')->nullable();
            $table->integer('ball')->default(0);
            $table->enum('status', ['active', 'finished', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_sessions');
    }
};
