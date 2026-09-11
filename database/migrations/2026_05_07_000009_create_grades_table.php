<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->integer('joriy_baho');
            $table->integer('oraliq_baho');
            $table->integer('joriy_oraliq');

            $table->integer('yakuniy_baho');
            $table->integer('umumiy');

            $table->string('davomat');

            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
