<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchantId = 1;

        $product = Product::create(
            ['name' => 'Burger', 'merchant_id' => $merchantId,'price' => 200],
            ['description' => 'Beef burger', 'price' => 50.00]
        );

        $ingredients = [
            ['name' => 'beef', 'quantity' => 150],
            ['name' => 'cheese', 'quantity' => 30],
            ['name' => 'onion', 'quantity' => 20],
        ];

        $ingredientIds = Ingredient::where('merchant_id', $merchantId)
            ->whereIn('name', array_column($ingredients, 'name'))
            ->pluck('id', 'name');

        $productIngredients = [];
        foreach ($ingredients as $ingredient) {
            if (isset($ingredientIds[$ingredient['name']])) {
                $productIngredients[] = [
                    'product_id' => $product->id,
                    'ingredient_id' => $ingredientIds[$ingredient['name']],
                    'quantity_required' => $ingredient['quantity'],
                    'unit_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('product_ingredients')->insert($productIngredients);
    }


}
