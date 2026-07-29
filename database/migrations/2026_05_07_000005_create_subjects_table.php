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
            $table->foreignId('kafedra_id')->constrained('kafedra')->onDelete('cascade'); // Kafedra bilan bog'lanish
            $table->foreignId('fakultet_id')->constrained('fakultet')->onDelete('cascade'); // Fakultet bilan bog'lanish
            $table->integer('kredit')->nullable(); // Kredit miqdori
            $table->foreignId('oquv_yili_id')->nullable()->constrained('oquv_yili')->onDelete('cascade'); // O'quv yili ID
            $table->string('talim_tili')->nullable(); // Talim tili

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
