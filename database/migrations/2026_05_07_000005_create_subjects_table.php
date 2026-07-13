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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id'); // Kategoriya ID
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade'); // Kategoriya bilan bog'lanish

            $table->unsignedBigInteger('teacher_id')->nullable(); // O'qituvchi (User) ID
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('lesson_type_id')->nullable(); // Dars turi ID
            $table->foreign('lesson_type_id')->references('id')->on('lesson_types')->onDelete('cascade'); // Dars turi bilan bog'lanish

            $table->string('semster'); // Semestr

            $table->string('nomi'); // Fan nomi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
