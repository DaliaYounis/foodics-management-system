<?php

namespace App\Services;

use App\Constants\OrderStatusConstants;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'number' => $this->generateOrderTrackingNumber(),
                'status' => OrderStatusConstants::SUBMITTED,
            ]);

            $productIds = collect($data['products'])->pluck('product_id');
            $products = Product::with(['ingredients' => function ($query) {
                $query->withPivot('unit_id', 'quantity_required');
            }])->whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');


            $orderProducts = [];
            foreach ($data['products'] as $productData) {
                $product = $products->get($productData['product_id']);

                $orderProducts[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_quantity' => $productData['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $this->stockService->updateStockQuantity($product, $productData['quantity']);
            }

            DB::table('order_products')->insert($orderProducts);

            Log::info('Order created successfully', ['order_id' => $order->id]);

            return $order;
        });
    }

    private function generateOrderTrackingNumber(): int
    {
        do {
            $trackingNumber = random_int(1000000000, 9999999999);
        } while (Order::where('number', $trackingNumber)->exists());

        return $trackingNumber;
    }
}



