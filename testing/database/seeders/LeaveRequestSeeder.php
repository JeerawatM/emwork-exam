<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // D. บันทึกข้อมูลทดสอบอย่าน้อย 10 รายการ
        LeaveRequest::truncate(); // Clear existing data

        $leaveTypes = ['sick_leave', 'personal_leave', 'annual_leave', 'other'];
        $statuses = ['pending', 'approved', 'rejected'];

        for ($i = 0; $i < 10; $i++) {
            $startDate = Carbon::now()->addDays(rand(0, 10)); // ล่วงหน้า 0-10 วัน
            $endDate = $startDate->copy()->addDays(rand(0, 3)); // ลา 0-3 วัน

            // ensure annual leave is at least 3 days in future and max 2 days duration
            $type = $leaveTypes[array_rand($leaveTypes)];
            if ($type === 'annual_leave') {
                $startDate = Carbon::now()->addDays(rand(3, 10));
                $endDate = $startDate->copy()->addDays(rand(0, 1)); // Max 2 days duration (0 days means 1 day leave)
            }

            // ensure no past dates for some
            if ($i % 3 == 0) { // create some past dates to test validation (which will be blocked by controller)
                $startDate = Carbon::now()->subDays(rand(1, 5));
                $endDate = $startDate->copy()->addDays(rand(0, 2));
            }


            LeaveRequest::create([
                'full_name' => 'พนักงาน ' . ($i + 1),
                'department_position' => 'แผนก ' . chr(65 + rand(0, 2)) . '/ตำแหน่ง ' . ($i % 2 == 0 ? 'หัวหน้า' : 'พนักงาน'),
                'email' => 'staff' . ($i + 1) . '@example.com',
                'phone_number' => '08' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                'leave_type' => $type,
                'reason' => 'มีธุระส่วนตัว / ไม่สบาย / พักผ่อนประจำปี',
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);
        }

        // Add a few specific cases for testing
        // Case 1: Annual Leave, less than 3 days in advance (should be rejected by validation)
        LeaveRequest::create([
            'full_name' => 'ทดสอบ ล่วงหน้าน้อยกว่า 3 วัน',
            'department_position' => 'HR',
            'phone_number' => '0812345678',
            'leave_type' => 'annual_leave',
            'reason' => 'ต้องการลาพักร้อนเร่งด่วน',
            'start_date' => Carbon::now()->addDays(1)->format('Y-m-d'), // 1 day in advance
            'end_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'status' => 'pending',
        ]);

        // Case 2: Annual Leave, more than 2 days duration (should be rejected by validation)
        LeaveRequest::create([
            'full_name' => 'ทดสอบ พักร้อนเกิน 2 วัน',
            'department_position' => 'Marketing',
            'phone_number' => '0812345679',
            'leave_type' => 'annual_leave',
            'reason' => 'เดินทางต่างจังหวัดหลายวัน',
            'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(7)->format('Y-m-d'), // 3 days duration
            'status' => 'pending',
        ]);

        // Case 3: Past Date Leave (should be rejected by validation)
        LeaveRequest::create([
            'full_name' => 'ทดสอบ วันลาย้อนหลัง',
            'department_position' => 'IT',
            'phone_number' => '0812345680',
            'leave_type' => 'sick_leave',
            'reason' => 'ไม่สบายกะทันหัน',
            'start_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'end_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'status' => 'pending',
        ]);
    }
}