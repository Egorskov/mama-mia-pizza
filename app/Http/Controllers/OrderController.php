<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Good;
use App\Models\GoodOption;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $orders = Order::getAllOrders();
        return response()->json($orders);
    }

    public function store(CreateOrderRequest $request)
    {
        try{
            $validated = $request->validated();
            $order = Order::createOrder($validated);
            $order->extracted($validated, $order);

            return response()->json(['message' => 'Order created successfully',
                'order' => $order->load(['items.good', 'items.goodOption', 'user_address'])
            ], 200);
        }
        catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 422);
        }
    }

    public function show(Order $order)
    {
        $order = Order::showOrder($order);
        return response()->json($order);
    }

    /**
     * @throws \Exception
     */
    public function cancel(Order $order)
    {
       try {
           $order = Order::where('user_id', auth()->user()->id)
               ->findOrFail($order->id);
           $order = $order->cancelOrder($order);

           return response()->json([
               'message' => 'Order successful canceled',
               'order' => $order
           ], 201);
       }
       catch (\Exception $exception){
           return response()->json([
               'message' => $exception->getMessage()
           ], 400);
       }
    }

}
