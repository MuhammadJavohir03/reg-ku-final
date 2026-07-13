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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id'); // Foydalanuvchi ID
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('subject_id'); // Fan ID
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->integer('joriy_baho'); // Joriy baho
            $table->integer('oraliq_baho'); // Oraliq baho
            $table->integer('joriy_oraliq'); // J+O baho

            $table->integer('yakuniy_baho'); // Yakuniy baho
            $table->integer('umumiy'); // Umumiy baho

            $table->string('davomat'); // Davomat holati

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
