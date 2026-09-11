<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('mini_semestrs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->unsignedBigInteger('bolim_id');
            $table->foreign('bolim_id')->references('id')->on('bolims')->onDelete('cascade');

            $table->integer('joriy_baho')->nullable();
            $table->integer('oraliq_baho')->nullable();
            $table->integer('joriy_oraliq')->nullable();

            $table->integer('yakuniy_baho')->nullable();
            $table->integer('umumiy')->nullable();
            $table->integer('davomat')->nullable();

            $table->boolean('status')->default(false);

            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('mini_semestrs');
    }
};
