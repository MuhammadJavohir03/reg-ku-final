<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('Talaba_ID')->unique()->nullable();
            $table->string('photo')->nullable();
            $table->string('To‘liq_ismi');
            $table->enum('role', ['admin', 'teacher', 'talaba'])->default('talaba');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('Fuqarolik')->nullable();
            $table->string('Davlat')->nullable();
            $table->string('Millat')->nullable();
            $table->string('Viloyat')->nullable();
            $table->string('Tuman')->nullable();
            $table->string('Jins')->nullable();
            $table->date('Tug‘ilgan_sana')->nullable();
            $table->string('Pasport_raqami')->nullable();
            $table->string('JSHSHIR_kod', 14)->nullable();
            $table->date('Pasport_berilgan_sana')->nullable();
            $table->string('Kurs')->nullable();
            $table->string('Fakultet')->nullable();
            $table->string('Guruh')->nullable();

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')   
                ->onDelete('set null');
                
            $table->string('Ta_lim_tili')->nullable();
            $table->string('O‘quv_yili')->nullable();
            $table->string('Semestr')->nullable();
            $table->string('Bitiruvchi')->nullable();
            $table->string('Mutaxassislik')->nullable();
            $table->string('Ta’lim_turi')->nullable();
            $table->string('Ta’lim_shakli')->nullable();
            $table->string('To‘lov_shakli')->nullable();
            $table->string('Grant_turi')->nullable();
            $table->text('Avvalgi_ta_lim_ma_lumoti')->nullable();
            $table->string('Talaba_toifasi')->nullable();
            $table->string('Ijtimoiy_toifa')->nullable();
            $table->integer('Birga_yashaydiganlar_soni')->nullable();
            $table->string('Birga_yashaydiganlar_toifasi')->nullable();
            $table->string('Yashash_joyi_statusi')->nullable();
            $table->string('Yashash_joyi_geolokatsiyasi')->nullable();
            $table->string('Buyruq')->nullable();
            $table->decimal('GPA', 4, 2)->nullable();
            $table->string('Kontrakt_N')->nullable();
            $table->string('Shartnoma_turi')->nullable();

            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
