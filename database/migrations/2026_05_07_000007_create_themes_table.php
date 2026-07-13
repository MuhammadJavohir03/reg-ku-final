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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('mini_semestr_id'); // Mini Semestr ID'si
            $table->foreign('mini_semestr_id')->references('id')->on('mini_semestrs')->onDelete('cascade');

            $table->string('nomi'); // Mavzu nomi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
