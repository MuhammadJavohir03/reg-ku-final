<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_joriy_baho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mavzu_id')->constrained('ms_mavzular')->onDelete('cascade');
            $table->integer('baho')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'mavzu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_joriy_baho');
    }
};