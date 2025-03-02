<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchants = [
            [
                'id' => 1,
                'email' => 'test1@test1.com',
                'phone' => '0523456789',
                'name' => 'Test1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'email' => 'test2@test2.com',
                'phone' => '0513456789',
                'name' => 'test2',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table("merchants")->insert($merchants);
    }
}
