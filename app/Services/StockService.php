<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockIngredient;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendLowStockNotification;
use App\Models\StockNotification;

class StockService
{

    public function updateStockQuantity(Product $product, int $quantity): void
    {
        $ingredientIds = $product->ingredients->pluck('id');
        $stockIngredients = StockIngredient::whereIn('ingredient_id', $ingredientIds)->get()->keyBy('ingredient_id');
        $units = Unit::whereIn('id', $product->ingredients->pluck('pivot.unit_id')->merge($stockIngredients->pluck('unit_id')))->get()->keyBy('id');

        $stockUpdates = [];

        foreach ($product->ingredients as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity_required * $quantity;
            $fromUnit = $units[$ingredient->pivot->unit_id] ?? null;
            $stockIngredient = $stockIngredients[$ingredient->id] ?? null;

            if (!$stockIngredient || !$fromUnit) {
                throw new \Exception("Invalid stock or unit data for ingredient ID {$ingredient->id}.");
            }

            $toUnit = $units[$stockIngredient->unit_id] ?? null;
            if (!$toUnit) {
                throw new \Exception("Invalid unit for ingredient ID {$ingredient->id}.");
            }

            $convertedQuantity = Unit::Convert($requiredQuantity, $fromUnit, $toUnit);

            if ($stockIngredient->quantity < $convertedQuantity) {
                throw new \Exception("Not enough stock for ingredient ID {$ingredient->id}.");
            }

            $stockUpdates[$stockIngredient->id] = $convertedQuantity;
        }

        foreach ($stockUpdates as $stockIngredientId => $convertedQuantity) {
            StockIngredient::where('id', $stockIngredientId)->decrement('quantity', $convertedQuantity);
            Log::info("Stock updated", ['stock_ingredient_id' => $stockIngredientId, 'decremented_by' => $convertedQuantity]);
        }

        $this->checkStockLevels($product->ingredients);
    }

    public function checkStockLevels($ingredients): void
    {
        $ingredientIds = $ingredients->pluck('id');
        $stockIngredients = StockIngredient::whereIn('ingredient_id', $ingredientIds)->get()->keyBy('ingredient_id');

        foreach ($ingredients as $ingredient) {
            $stockIngredient = $stockIngredients[$ingredient->id] ?? null;
            if (!$stockIngredient) {
                continue;
            }

            $merchantId = $stockIngredient->merchant_id ?? null;
            if (!$merchantId) {
                Log::warning("Missing merchant_id for ingredient ID {$ingredient->id}.");
                continue;
            }

            $threshold = $ingredient->alert_threshold_percentage * 0.01;

            $existingNotification = StockNotification::where('ingredient_id', $ingredient->id)
                ->where('merchant_id', $merchantId)
                ->where('is_notified', true)
                ->first();
            Log::info("test" .$threshold);

            if (($stockIngredient->quantity <= $threshold) && !$existingNotification) {

                SendLowStockNotification::dispatch($ingredient);

                Log::info("Low stock notification sent", ['ingredient_id' => $ingredient->id, 'merchant_id' => $merchantId]);

                StockNotification::create(
                    ['ingredient_id' => $ingredient->id, 'merchant_id' => $merchantId, 'is_notified' => true, 'sent_at' => now()],
                );
            } elseif ($stockIngredient->quantity > $threshold && $existingNotification) {

                $existingNotification->update(['is_notified' => false]);
            }
        }
    }

}
