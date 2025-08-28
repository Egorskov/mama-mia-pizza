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
        $order = Order::with(['items.good', 'items.goodOption', 'user_address'])
            ->where('user_id', auth()->user()->id)
            ->get();
        return response()->json($order);
    }

    public function store(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'user_address_id' => $validated->user_address_id,
            'delivery_time' => $validated->delivery_time,
        ]);
        $this->extracted($validated, $order);

        return response()->json(['message' => 'Order created successfully',
            'order' => $order->load(['items.good', 'items.goodOption', 'user_address'])
        ], 201);
    }

    public function show(Order $order)
    {
        $order = Order::with(['items.good', 'items.goodOption', 'user_address'])
            ->where('user_id', auth()->user()->id)
            ->findOrFail($order->id);
        return response()->json($order);
    }

    public function cancel(Order $order)
    {
        $order = Order::where('user_id', auth()->user()->id)
            ->findOrFail($order->id);
        if($order->delivery_status !== 'pending'){
            return response()->json([
                'message' => 'Cannot cancel order status -'
                    . $order->delivery_status . '. Please, call Alex Shevchenko'
            ], 400);
        }
        $order->update(['delivery_status' => 'cancelled']);

        return response()->json([
            'message' => 'Order successful canceled',
            'order'=>$order
        ], 201);

    }

}
