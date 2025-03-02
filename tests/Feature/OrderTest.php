<?php

namespace Tests\Feature;

use App\Jobs\SendLowStockNotification;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockIngredient;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;


class OrderTest extends TestCase
{

    public function test_it_creates_an_order_and_updates_stock()
    {
        Queue::fake();

        $product = Product::create([
            'name' => 'Double Potato Sandwich',
            'merchant_id' => 1,
            'price' => 200,
            'description' => 'Potato Sandwich'
        ]);

        $ingredient = Ingredient::create([
            'alert_threshold_percentage' => 50,
            'name' => 'potato',
            'merchant_id' => 1
        ]);

        $product->ingredients()->attach($ingredient->id, [
            'unit_id' => 1, // grams
            'quantity_required' => 600, // 600 grams required
        ]);

        $stockIngredient = StockIngredient::create([
            'ingredient_id' => $ingredient->id,
            'merchant_id' => 1,
            'quantity' => 100, // 100 kg
            'unit_id' => 2, // kilograms
        ]);

        $order = Order::create([
            'merchant_id' => 1,
            'number' => rand(1000000000, 9999999999),
            'status' => 'submitted',
        ]);

        DB::table('order_products')->insert([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_quantity' => 1,
        ]);

        $stockService = new StockService();

        $stockService->updateStockQuantity($product, 1);

        $remainingStock = 100 - (600 / 1000); // 100 - 0.6 = 99.4

        $this->assertDatabaseHas('stock_ingredients', [
            'ingredient_id' => $ingredient->id,
            'quantity' => $remainingStock,
        ]);

        $this->assertDatabaseHas('order_products', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
    }



    public function test_stock_ingredients_not_enough()
    {
        Queue::fake();

        $product = Product::create([
            'name' => 'Double Chicken Burger',
            'merchant_id' => 1,
            'price' => 200,
            'description' => 'Chicken Burger'
        ]);

        $ingredient = Ingredient::create([
            'alert_threshold_percentage' => 50,
            'name' => 'chicken',
            'merchant_id' => 1
        ]);

        $product->ingredients()->attach($ingredient->id, [
            'unit_id' => 1,
            'quantity_required' => 600,
        ]);

        StockIngredient::create([
            'ingredient_id' => $ingredient->id,
            'merchant_id' => 1,
            'quantity' => 0.5,
            'unit_id' => 2,
        ]);

        $stockService = new StockService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Not enough stock for ingredient ID {$ingredient->id}.");

        $stockService->updateStockQuantity($product, 1);
    }

    public function test_multiple_products_reduce_stock_correctly()
    {
        $ingredient = Ingredient::factory()->create();
        $product1 = Product::create([
            'name' => 'Fettuccine Alfredo',
            'merchant_id' => 1,
            'price' => 200,
            'description' => 'Fettuccine Alfredo'
        ]);
        $product2 = Product::create([
            'name' => 'Risotto',
            'merchant_id' => 1,
            'price' => 200,
            'description' => 'Risotto'
        ]);

        $product1->ingredients()->attach($ingredient->id, ['unit_id' => 1, 'quantity_required' => 400]);
        $product2->ingredients()->attach($ingredient->id, ['unit_id' => 1, 'quantity_required' => 300]);

        StockIngredient::create(['ingredient_id' => $ingredient->id, 'quantity' => 2, 'unit_id' => 2, 'merchant_id' => 1]); // 2kg

        $stockService = new StockService();
        $stockService->updateStockQuantity($product1, 1);
        $stockService->updateStockQuantity($product2, 1);

        $this->assertDatabaseHas('stock_ingredients', [
            'ingredient_id' => $ingredient->id,
            'quantity' => 2 - ((400 + 300) / 1000),
        ]);
    }

    public function test_notification_triggered_when_stock_below_threshold()
    {
        Queue::fake();

        $ingredient = Ingredient::factory()->create(['alert_threshold_percentage' => 50]);
        StockIngredient::create(['ingredient_id' => $ingredient->id, 'quantity' => 0.4, 'unit_id' => 2, 'merchant_id' => 1]); // 0.4kg

        $stockService = new StockService();
        $stockService->checkStockLevels(collect([$ingredient]));

        Queue::assertPushed(SendLowStockNotification::class);
    }

    public function test_it_dispatches_low_stock_notification()
    {
        Queue::fake();

        $product = Product::create([
            'name' => 'Pizza',
            'merchant_id' => 1,
            'price' => 200,
            'description' => 'Pizza'
        ]);
        $ingredient = Ingredient::create([
            'alert_threshold_percentage' => 50,
            'name' => 'tomato',
            'merchant_id' => 1
        ]);

        $product->ingredients()->attach($ingredient->id, [
            'unit_id' => 1,
            'quantity_required' => 600,
        ]);

        StockIngredient::create([
            'ingredient_id' => $ingredient->id,
            'merchant_id' => 1,
            'quantity' => 1,
            'unit_id' => 2,
        ]);

        $stockService = new StockService();
        $stockService->updateStockQuantity($product,1);

        Queue::assertPushed(SendLowStockNotification::class, function ($job) use ($ingredient) {
            return $job->ingredient->id === $ingredient->id;
        });

        $this->assertDatabaseHas('stock_notifications', [
            'ingredient_id' => $ingredient->id,
            'is_notified' => true,
        ]);
    }


}
