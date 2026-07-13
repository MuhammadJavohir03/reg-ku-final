<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Masalan: "Hero bo'limi", "Hemis bo'limi", "Kontrakt"
            $table->string('slug')->unique()->nullable();
            $table->string('icon')->nullable();      // frontendda ikonka uchun (ixtiyoriy)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};