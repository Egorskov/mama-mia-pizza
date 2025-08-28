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
        $this->middleware('admin')->only([
            'adminIndex', 'adminOrder', 'adminId', 'adminUpdate']);
    }

    public function index()
    {
        $order = Order::with(['items.good', 'items.goodOption', 'user_address'])
            ->where('user_id', auth()->user()->id)
            ->get();
        return response()->json($order);
    }

    public function adminIndex()
    {
        $order = Order::with(['user', 'items.good', 'items.goodOption', 'user_address'])
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
            //'delivery_status' => $request->delivery_status
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

    /**
     *  Просмотр любого заказа по номеру админом
     */
    public function adminOrder(Order $order)
    {
        $order = Order::with(['items.good', 'items.goodOption', 'user_address'])
            ->findOrFail($order->id);
        return response()->json($order);
    }

    /**
     *  Просмотр заказов любого пользователя админом
     */

    public function adminId($id)
    {
        $order = Order::with(['items.good', 'items.goodOption', 'user_address'])
            ->where('user_id', $id)
            ->get();
        return response()->json($order);
    }

    /**
     * пользователю нельзя обновлять заказ, только через админа
     */

    public function adminUpdate(UpdateOrderRequest $request, Order $order)
    {
        //$order = Order::findOrFail($order->id);
        $validated = $request->validated();
//        $order = Order::update([
//            'user_address_id' => $validated->user_address_id,
//            'delivery_time' => $validated->delivery_time,
//            'delivery_status' => $validated->delivery_status
//        ]);
        $updateData = [];

        if(isset($validated['user_address_id'])){
            $updateData['user_address_id'] = $validated['user_address_id'];
        }
        if(isset($validated['delivery_time'])){
            $updateData['delivery_time'] = $validated['delivery_time'];
        }
        if(isset($validated['delivery_status'])){
            $updateData['delivery_status'] = $validated['delivery_status'];
        }
        $order->update($validated);
        if(isset($validated['items'])){
            OrderItem::where('order_id', $order->id)->delete();
            $this->extracted($validated, $order);
        }
        return response()->json(['message' => 'Order update successfully',
            'order' => $order->load(['items.good', 'items.goodOption', 'user_address'])
        ], 200);

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

    /**
     * @param mixed $validated
     * @param Order $order
     * @return void
     */
    public function extracted(mixed $validated, Order $order): void
    {
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $good = Good::findOrFail($item['good_id']);
            $basePrice = $good->price;
            $quantity = $item['quantity'];
            $goodOptionId = null;
            $optionPrice = 0;
            $totalPrice = $basePrice * $quantity;

            if (!empty($item['good_option_id'])) {
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
    }

}
