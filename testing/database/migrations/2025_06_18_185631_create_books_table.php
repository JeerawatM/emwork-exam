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
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('title'); // ชื่อหนังสือ (ข้อความ)
            $table->string('author')->nullable(); // ผู้แต่ง (ข้อความ, อนุญาตให้ว่างได้)
            $table->text('description')->nullable(); // รายละเอียด (ข้อความยาว, อนุญาตให้ว่างได้)
            $table->string('isbn')->unique()->nullable(); // ISBN (ข้อความ, ไม่ซ้ำกัน, อนุญาตให้ว่างได้)
            $table->integer('publication_year')->nullable(); // ปีที่พิมพ์ (ตัวเลข, อนุญาตให้ว่างได้)
            $table->timestamps(); // created_at และ updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
