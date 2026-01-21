<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Calls;

class CallsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed a realistic distribution of calls
        Calls::factory()
            ->count(300)
            ->create();
    }
}
