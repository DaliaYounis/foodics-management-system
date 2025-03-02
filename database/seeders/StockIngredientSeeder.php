<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $merchantId = 1;

        $stockData = [
            'beef' => 20,
            'cheese' => 5,
            'onion' => 1,
        ];

        $unitId = 2;

        $ingredients = Ingredient::whereIn('name', array_keys($stockData))
            ->where('merchant_id', $merchantId)
            ->get(['id', 'name']);

        $stockIngredients = $ingredients->map(function ($ingredient) use ($stockData, $merchantId, $unitId) {
            return [
                'ingredient_id' => $ingredient->id,
                'merchant_id' => $merchantId,
                'quantity' => $stockData[$ingredient->name],
                'unit_id' => $unitId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('stock_ingredients')->insert($stockIngredients);
    }

}
