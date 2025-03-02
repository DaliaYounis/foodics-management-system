<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
        public function run()
    {
        $merchantId = 1;

        $ingredients = [
            ['id' => 1 , 'name' => 'beef', 'alert_threshold_percentage' => 50, 'merchant_id' => $merchantId, 'created_at' => now(),'updated_at' => now()],
            ['id' => 2 , 'name' => 'cheese', 'alert_threshold_percentage' => 50, 'merchant_id' => $merchantId, 'created_at' => now(),'updated_at' => now()],
            ['id' => 3 , 'name' => 'onion', 'alert_threshold_percentage' => 50, 'merchant_id' => $merchantId,'created_at' => now(),'updated_at' => now()],
        ];

        DB::table('ingredients')->insert($ingredients);

    }
}
