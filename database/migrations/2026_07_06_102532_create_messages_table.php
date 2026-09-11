<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();



            $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->cascadeOnDelete();

            $table->text('body');

            $table->unsignedTinyInteger('status')->default(0);




            $table->unsignedTinyInteger('rozilik')->nullable();

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['sender_id', 'receiver_id']);
            $table->index(['section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};