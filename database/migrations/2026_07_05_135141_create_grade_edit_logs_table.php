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

            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();



            $table->string('editable_type', 20);

            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('field', 30)->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('mavzu_id')->nullable();

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
