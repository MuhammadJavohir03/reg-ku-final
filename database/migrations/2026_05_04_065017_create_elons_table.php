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
        Schema::create('elons', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('admin_id'); // O'qituvchi (User) ID
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->string('title', 255);
            $table->string('short_content', 255);
            $table->text('full_content');
            $table->string('photo', 255);
            
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')
                ->references('id')         // 'categories' jadvalidagi 'id' ustuniga
                ->on('categories')   
                ->onDelete('set null');  

            $table->string('kurs', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elons');
    }
};
