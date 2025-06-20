<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\LeaveRequestSeeder; // Import seeder ของเรา

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LeaveRequestSeeder::class, // เรียกใช้ LeaveRequestSeeder
        ]);
    }
}