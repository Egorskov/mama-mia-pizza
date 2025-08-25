<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
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
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'user_address_id' => $request->user_address_id,
            'delivery_time' => $request->delivery_time,
            //'delivery_status' => $request->delivery_status
        ]);
        $totalAmount = 0;

        foreach ($request->items as $item){
            $good = Good::findOrFail($item['good_id']);
            $basePrice = $good->price;
            $quantity = $item['quantity'];
            $goodOptionId = null;
            $optionPrice = 0;
            $totalPrice = $basePrice * $quantity;

            if(!empty($item['good_option_id'])){
                $goodOption = GoodOption::findOrFail($item['good_option_id']);
                $goodOptionId = $goodOption->id;
                $optionPrice = $goodOption->price;
                $totalPrice = ($basePrice + $optionPrice) * $item['quantity'];
            }

            OrderItem::create([
                'order_id' => $order->id,
                'good_id' => $good->id,
                'quantity' => $quantity,
                'good_option_id' => $goodOptionId,
                'base_price' => $basePrice,
                'option_price' => $optionPrice,
                'total_price' => $totalPrice,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $totalAmount += $totalPrice;
        }

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


    /**
     * пользователю нельзя обновлять заказ, только через админа
     */
//    public function update(Request $request, Order $order)
//    {
//
//    }
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
