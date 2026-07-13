<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_mavzular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bolim_id')->constrained('bolims')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('nomi');                          // mavzu/oraliq/yakuniy nomi
            $table->enum('tur', ['mavzu', 'oraliq', 'yakuniy'])->default('mavzu');
            $table->integer('tartib')->default(0);           // tartib raqami
            $table->boolean('faol')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_mavzular');
    }
};
