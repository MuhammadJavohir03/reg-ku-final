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

            // Kimdan (har doim to'ldiriladi)
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            // Kimga - ikkita variantdan FAQAT bittasi to'ldiriladi:
            // 1) receiver_id  -> talaba-talaba yozishmasi (yoki admin javobida aniq talabaga)
            // 2) section_id   -> talaba bo'limga (adminlar guruhiga) yozganda
            $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->cascadeOnDelete();

            $table->text('body');

            // 0 = o'qilmagan (bitta "✓"), 1 = o'qilgan (ikkita ko'k "✓✓")
            $table->unsignedTinyInteger('status')->default(0);

            // Faqat talaba<->talaba yozishmasida ishlatiladi:
            // null  -> bu yozishma turi uchun ahamiyatsiz (bo'lim chatlarida doim null qoladi)
            // 0     -> so'rov yuborilgan, qabul qiluvchi hali rozilik bermagan
            // 1     -> qabul qiluvchi rozilik bergan, chat oddiy davom etadi
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