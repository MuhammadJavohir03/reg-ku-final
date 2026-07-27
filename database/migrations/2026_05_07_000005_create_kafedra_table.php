<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kafedra', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fakultet_id')
                  ->constrained('fakultet')
                  ->cascadeOnDelete();

            $table->string('nomi');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kafedra');
    }
};