<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_edit_logs', function (Blueprint $table) {
            $table->id();

            // Bahoni o'zgartirgan admin/o'qituvchi
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();

            // free_yakuniy   -> free_semestr.yakuniy_baho
            // mini_summary   -> mini_semestr.{joriy_baho|oraliq_baho|joriy_oraliq|yakuniy_baho|umumiy}
            // mini_topic     -> ms_joriy_baho (bitta mavzu bo'yicha baho)
            $table->string('editable_type', 20);

            $table->unsignedBigInteger('record_id')->nullable();  // free_semestr / mini_semestr id (summary uchun)
            $table->string('field', 30)->nullable();              // mini_summary uchun ustun nomi
            $table->unsignedBigInteger('student_id')->nullable(); // mini_topic uchun talaba (user) id
            $table->unsignedBigInteger('mavzu_id')->nullable();   // mini_topic uchun mavzu id

            $table->decimal('old_value', 5, 2)->nullable();
            $table->decimal('new_value', 5, 2)->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['editable_type', 'record_id', 'field']);
            $table->index(['editable_type', 'student_id', 'mavzu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_edit_logs');
    }
};
