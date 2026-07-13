<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mini_semestrs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id'); // Foydalanuvchi ID'si
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('subject_id'); // Fan ID'si
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->unsignedBigInteger('bolim_id');
            $table->foreign('bolim_id')->references('id')->on('bolims')->onDelete('cascade');

            $table->integer('joriy_baho')->nullable(); // Joriy baho
            $table->integer('oraliq_baho')->nullable(); // Oraliq baho
            $table->integer('joriy_oraliq')->nullable(); // J+O baho

            $table->integer('yakuniy_baho')->nullable(); // Yakuniy baho
            $table->integer('umumiy')->nullable(); // Umumiy baho
            $table->integer('davomat')->nullable(); // Davomat foizi

            $table->boolean('status')->default(false); // Status (fan amaldami yoki yoq)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mini_semestrs');
    }
};
