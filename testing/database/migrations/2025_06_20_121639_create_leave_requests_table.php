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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // ชื่อ - นามสกุล (required)
            $table->string('department_position')->nullable(); // สังกัด/ตำแหน่ง
            $table->string('email')->nullable(); // อีเมล์
            $table->string('phone_number'); // เบอร์โทรศัพท์ (required)
            $table->enum('leave_type', ['sick_leave', 'personal_leave', 'annual_leave', 'other'])->default('other'); // ประเภทการลา (required)
            $table->text('reason'); // สาเหตุการลา (required)
            $table->date('start_date'); // วันที่ขอลา (เริ่มต้น) (required)
            $table->date('end_date'); // ถึงวันที่ (สิ้นสุด) (required)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // สถานะ (default: รอพิจารณา)
            $table->timestamps(); // วันเวลาที่บันทึกข้อมูล (created_at) และ วันเวลาที่ปรับปรุงข้อมูลล่าสุด (updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};