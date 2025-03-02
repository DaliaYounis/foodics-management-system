<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            ['id' => 1 ,'name' => 'Gram', 'symbol' => 'g', 'description' => 'Measurement of weight', 'conversion_factor' => 1, 'created_at' => now(),'updated_at' => now()],
            ['id' => 2 ,'name' => 'Kilogram', 'symbol' => 'kg', 'description' => 'Measurement of weight', 'conversion_factor' => 1000, 'created_at' => now(),'updated_at' => now()],
            ['id' => 3 ,'name' => 'Milliliter', 'symbol' => 'ml', 'description' => 'Measurement of volume', 'conversion_factor' => 1, 'created_at' => now(),'updated_at' => now()],
            ['id' => 4 ,'name' => 'Liter', 'symbol' => 'L', 'description' => 'Measurement of volume', 'conversion_factor' => 1000, 'created_at' => now(),'updated_at' => now()],
            ['id' => 5 ,'name' => 'Piece', 'symbol' => 'pcs', 'description' => 'Countable item', 'conversion_factor' => 1, 'created_at' => now(),'updated_at' => now()],
        ];

        DB::table('units')->insert($units);

    }
}
