<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only([
            'adminIndex', 'adminOrder', 'adminId', 'adminUpdate']);
    }

    public function adminIndex(): JsonResponse
    {
        $orders = Order::getAllOrdersAllUsers();
        return response()->json($orders, 200);
    }

    /**
     *  Просмотр любого заказа по номеру админом
     */
    public function adminOrder(Order $order): JsonResponse
    {
        $order = Order::getOrdersWithId($order);
        return response()->json($order, 200);
    }

    /**
     *  Просмотр заказов любого пользователя админом
     */

    public function adminId($orderId): JsonResponse
    {
        $order = Order::getOrderById($orderId);
        return response()->json($order, 200);
    }

    /**
     * пользователю нельзя обновлять заказ, только через админа
     */

    public function adminUpdate(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        try {
            $order->updateOrderByAdmin($validated);
            if(isset($validated['items'])){
                Order::deleteOrdersItem($order);
                $order->extracted($validated, $order);
            }
            return response()->json(['message' => 'Order update successfully',
                'order' => $order->load(['items.good', 'items.goodOption', 'user_address'])
            ], 200);
        }
        catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 422);
        }
    }
}
