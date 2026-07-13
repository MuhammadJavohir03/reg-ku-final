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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Excel ustunlari bilan bir xil nomlangan ustunlar
            $table->string('Talaba_ID')->unique()->nullable(); // Talaba ID
            $table->string('photo')->nullable(); // Rasm
            $table->string('To‘liq_ismi'); // To‘liq ismi
            $table->enum('role', ['admin', 'teacher', 'talaba'])->default('talaba'); //rollar
            $table->string('email')->unique()->nullable(); // Email
            $table->string('password')->nullable(); // Password
            $table->string('Fuqarolik')->nullable(); // Fuqarolik
            $table->string('Davlat')->nullable(); // Davlat
            $table->string('Millat')->nullable(); // Millat
            $table->string('Viloyat')->nullable(); // Viloyat
            $table->string('Tuman')->nullable(); // Tuman
            $table->string('Jins')->nullable(); // Jins
            $table->date('Tug‘ilgan_sana')->nullable(); // Tug‘ilgan sana
            $table->string('Pasport_raqami')->nullable(); // Pasport raqami
            $table->string('JSHSHIR_kod', 14)->nullable(); // JSHSHIR-kod
            $table->date('Pasport_berilgan_sana')->nullable(); // Pasport berilgan sana
            $table->string('Kurs')->nullable(); // Kurs
            $table->string('Fakultet')->nullable(); // Fakultet
            $table->string('Guruh')->nullable(); // Guruh

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')
                ->references('id')         // 'categories' jadvalidagi 'id' ustuniga
                ->on('categories')   
                ->onDelete('set null');   // Agar yo'nalish o'chirilsa, talaba o'chib ketmasin
                
            $table->string('Ta_lim_tili')->nullable(); // Ta'lim tili
            $table->string('O‘quv_yili')->nullable(); // O‘quv yili
            $table->string('Semestr')->nullable(); // Semestr
            $table->string('Bitiruvchi')->nullable(); // Bitiruvchi
            $table->string('Mutaxassislik')->nullable(); // Mutaxassislik
            $table->string('Ta’lim_turi')->nullable(); // Ta’lim turi
            $table->string('Ta’lim_shakli')->nullable(); // Ta’lim shakli
            $table->string('To‘lov_shakli')->nullable(); // To‘lov shakli
            $table->string('Grant_turi')->nullable(); // Grant turi
            $table->text('Avvalgi_ta_lim_ma_lumoti')->nullable(); // Avvalgi ta'lim ma'lumoti
            $table->string('Talaba_toifasi')->nullable(); // Talaba toifasi
            $table->string('Ijtimoiy_toifa')->nullable(); // Ijtimoiy toifa
            $table->integer('Birga_yashaydiganlar_soni')->nullable(); // Birga yashaydiganlar soni
            $table->string('Birga_yashaydiganlar_toifasi')->nullable(); // Birga yashaydiganlar toifasi
            $table->string('Yashash_joyi_statusi')->nullable(); // Yashash joyi statusi
            $table->string('Yashash_joyi_geolokatsiyasi')->nullable(); // Yashash joyi geolokatsiyasi
            $table->string('Buyruq')->nullable(); // Buyruq
            $table->decimal('GPA', 4, 2)->nullable(); // GPA
            $table->string('Kontrakt_N')->nullable(); // Kontrakt №
            $table->string('Shartnoma_turi')->nullable(); // Shartnoma turi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
