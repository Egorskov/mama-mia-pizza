<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Good;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserAddress;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderControllerTest extends TestCase
{
    /**
     * A basic test example.
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);
        $this->userHeader = [
            'Authorization' => "Bearer $this->userToken",
            'Accept' => 'application/json'
        ];
        UserAddress::factory()
            ->count(rand(1,3))->create([
                'user_id' => $this->user->id
            ]);
        $this->order = Order::factory()
            ->count(rand(1, 3))->create([
                'user_id' => $this->user->id,
                'user_address_id' => $this->user->addresses->random()->id
            ]);
        $this->order->each(function ($order) {
            OrderItem::factory()
                ->count(rand(1, 5))
                ->create([
                    'order_id' => $order->id
                ]);
        });

 //       $this->userNotAuth = User::factory()->create();
    }

    public function test_order_index_200(): void
    {
        $response = $this->withHeaders($this->userHeader)
            ->getJson('/api/orders');
       // dump($response->json());
        $response->assertStatus(200);
    }

    public function test_order_index_401(): void
    {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(401);
    }

    public function test_order_show_200(): void
    {
        $order = $this->order->first();
        $response = $this->withHeaders($this->userHeader)
            ->getJson('/api/orders/' . $order->id);
        $response->assertStatus(200);
    }

    public function test_order_show_401(): void
    {
        $response = $this->getJson('/api/orders/1');
        $response->assertStatus(401);
    }

    public function test_order_create_200(): void
    {
        $good = Good::factory()->create();
        $request = [
            'user_address_id' => $this->user->addresses->random()->id,
            'delivery_time' => fake()->dateTimeBetween('now', '+1 hour')
                ->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'good_id' => $good->id,
                    'quantity' => 1,
                    'good_option_id' => null
                ]
            ]
        ];
        $response = $this->withHeaders($this->userHeader)
            ->postJson('/api/orders', $request);
        $response->assertStatus(200);
    }

    public function test_order_create_422(): void
    {
        $good = Good::factory()->create();
        $request = [
            'user_address_id' => $this->user->addresses->random()->id,
            'delivery_time' => fake()->dateTimeBetween('now', '+1 hour'),
            'items' => [
                [
                    'good_id' => $good->id,
                    'quantity' => 1,
                    'good_option_id' => null
                ]
            ]
        ];
        $response = $this->withHeaders($this->userHeader)
            ->postJson('/api/orders', $request);
        $response->assertStatus(422);
    }

    public function test_order_create_401(): void
    {
        $good = Good::factory()->create();
        $request = [
            'user_address_id' => $this->user->addresses->random()->id,
            'delivery_time' => fake()->dateTimeBetween('now', '+1 hour')
                ->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'good_id' => $good->id,
                    'quantity' => 1,
                    'good_option_id' => null
                ]
            ]
        ];
        $response = $this->postJson('/api/orders', $request);
        $response->assertStatus(401);
    }

    public function test_cancel_201(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'user_address_id' => $this->user->addresses->random()->id,
            'delivery_status' => 'pending']);
        $response = $this->withHeaders($this->userHeader)
            ->postJson('/api/orders/' . $order->id . '/cancel');
        $response->assertStatus(201);
    }

    public function test_cancel_400(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'user_address_id' => $this->user->addresses->random()->id,
            'delivery_status' => 'delivered']);
        $response = $this->withHeaders($this->userHeader)
            ->postJson('/api/orders/' . $order->id . '/cancel');
        $response->assertStatus(400);
    }

    public function test_cancel_401(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'user_address_id' => $this->user->addresses->random()->id,
            'delivery_status' => 'delivered']);
        $response = $this->postJson('/api/orders/' . $order->id . '/cancel');
        $response->assertStatus(401);
    }
}
