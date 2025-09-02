<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_address_id',
        'delivery_time',
        'delivery_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function getAllOrders()
    {
        return self::with(['items.good', 'items.goodOption', 'user_address'])
            ->where('user_id', auth()->user()->id)
            ->get();
    }

    public static function createOrder($validated)
    {
        return self::create([
            'user_id' => auth()->user()->id,
            'user_address_id' => $validated['user_address_id'],
            'delivery_time' => $validated['delivery_time'],
        ]);
    }

    public static function showOrder($order)
    {
        return self::with(['items.good', 'items.goodOption', 'user_address'])
            ->where('user_id', auth()->user()->id)
            ->findOrFail($order->id);
    }

    /**
     * @throws \Exception
     */
    public function cancelOrder($order): static
    {
        if($this->delivery_status !== 'pending'){
            throw new \Exception('Cannot cancel order status -'
                    . $order->delivery_status . '. Please, call Alex Shevchenko');
        }
        $this->update(['delivery_status' => 'cancelled']);
        return $this;
    }

    public function extracted(mixed $validated, Order $order): void
    {
        $totalAmount = 0;
        $countPizza = 0;
        $countDrink = 0;
        $limits = config('item_limits');
        foreach ($validated['items'] as $item) {
            $good = Good::findOrFail($item['good_id']);
            $quantity = (int) $item['quantity'];
            if($good->category == 'pizza'){
                $countPizza += $quantity;
                if ($countPizza > $limits['pizza']) {
                    throw new \Exception(
                        "pizza limit exceeded, max limit = {$limits['pizza']} pcs"
                    );
                }
            } else {
                $countDrink += $quantity;
                if ($countDrink > $limits['drink']) {
                    throw new \Exception(
                        "drink limit exceeded, max limit = {$limits['drink']} pcs"
                    );
                }
            }
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
