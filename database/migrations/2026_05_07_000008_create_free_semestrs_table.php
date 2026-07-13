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
        Schema::create('free_semestrs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id'); // Foydalanuvchi ID'si
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('subject_id'); // Fan ID'si
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->unsignedBigInteger('bolim_id');
            $table->foreign('bolim_id')->references('id')->on('bolims')->onDelete('cascade');
            $table->string('joriy_baho')->nullable();
            $table->string('oraliq_baho')->nullable();
            $table->string('joriy_oraliq')->nullable();
            $table->string('yakuniy_baho')->nullable();
            $table->string('umumiy')->nullable();
            $table->string('davomat')->nullable();

            $table->boolean('status')->default(false); // Status (fan amaldami yoki yoq)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_semestrs');
    }
};
