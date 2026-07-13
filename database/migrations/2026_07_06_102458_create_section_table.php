<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Qaysi admin (user_id) qaysi bo'limga (section_id) biriktirilgan.
        // Bitta bo'limga bir nechta admin, bitta adminga bir nechta bo'lim biriktirish mumkin.
        Schema::create('section_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // admin
            $table->timestamps();

            $table->unique(['section_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_user');
    }
};
