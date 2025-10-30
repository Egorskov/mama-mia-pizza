<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only([
            'adminIndex', 'adminOrder', 'adminId', 'adminUpdate']);
    }

    public function adminIndex()
    {
        $order = Order::with(['user', 'items.good', 'items.goodOption', 'user_address'])
            ->get();
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
        $validated = $request->validated();
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
        $order->update($updateData);
        if(isset($validated['items'])){
            OrderItem::where('order_id', $order->id)->delete();
            $this->extracted($validated, $order);
        }
        return response()->json(['message' => 'Order update successfully',
            'order' => $order->load(['items.good', 'items.goodOption', 'user_address'])
        ], 200);

    }
}
