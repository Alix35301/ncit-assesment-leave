<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Leave;
use App\Models\LeaveBalance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ncit.com',
            'is_admin' => true,

        ]);

        LeaveBalance::create([
            'user_id' => 1,
            'annual_leave' => 30,
            'sick_leave' => 30,
            'other_leave' => 0,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'User',
            'email' => 'user@ncit.com',
        ]);




        \App\Models\Leave::create([
            'user_id' => 2,
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-01',
            'status' => 'pending',
            'type' => 'annual',
            'reason' => 'Test',
            'type' => 'annual',
        ]);
        $totalDays = Carbon::parse('2025-01-01')->diffInDays(Carbon::parse('2025-01-01')) + 1;


        
        $leaveBalance = \App\Models\LeaveBalance::create([
            'user_id' => 2,
            'annual_leave' => 29,
            'sick_leave' => 30,
            'other_leave' => 0,
        ]);






        
    }
}

