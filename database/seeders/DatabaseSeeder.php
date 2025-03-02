<?php

namespace Database\Seeders;

use App\Models\StockIngredient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MerchantSeeder::class,
            UnitSeeder::class,
            IngredientSeeder::class,
            ProductSeeder::class,
            StockIngredientSeeder::class,]);
    }
}
