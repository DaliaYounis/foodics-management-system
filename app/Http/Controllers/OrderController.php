<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;

    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return response()->success('Order created successfully!', ['order' => $order]);
        } catch (\Exception $e) {
            return response()->error('Failed to create order.', ['order'=>'', 'error' => $e->getMessage()], 500);
        }
    }

}
