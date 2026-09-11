<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_topshiriqlar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ms_material_id');
            $table->unsignedBigInteger('user_id');
            $table->string('pdf_path')->nullable();
            $table->decimal('ball', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('ms_material_id')
                ->references('id')
                ->on('ms_materiallar')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique(['ms_material_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_topshiriqlar');
    }
};
