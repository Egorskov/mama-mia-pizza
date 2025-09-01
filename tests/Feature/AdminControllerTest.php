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
use function PHPUnit\Framework\assertJson;

class AdminControllerTest extends TestCase
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

        $this->admin = User::factory()->admin()->create();
        $this->adminToken = JWTAuth::fromUser($this->admin);
        $this->adminHeader = [
            'Authorization' => "Bearer $this->adminToken",
            'Accept' => 'application/json'
        ];

    }
    public function testAdminOrderIndexExpectHttpOkAndShowAllOrdersOfAllUsers(): void
    {
        $response = $this->withHeaders($this->adminHeader)
            ->getJson('/api/admin/orders');
        $response->assertStatus(200);
    }

    public function testAdminOrderIndexExpectHttpUnauthenticated(): void
    {
        $response = $this->withHeaders($this->userHeader)
            ->getJson('/api/admin/orders');
        $response->assertStatus(401);
    }

    public function testAdminOrderShowExpectHttpOkAndShowOrdersWithId(): void
    {
        $order = Order::factory()->create();
        $id = $order->id;
        $response = $this->withHeaders($this->adminHeader)
            ->getJson('/api/admin/orders/' . $id);
        $response->assertStatus(200);
    }

    public function testAdminOrderShowExpectHttpUnauthenticated(): void
    {
        $order = Order::factory()->create();
        $order_id = $order->id;
        $response = $this->withHeaders($this->userHeader)
            ->getJson('/api/admin/orders/' . $order_id);
        $response->assertStatus(401);
    }

    public function testAdminUsersOrdersShowExpectHttpOkAndShowUsersOrdersWithUser_Id(): void
    {
        $user_id = $this->user->id;
        $response = $this->withHeaders($this->adminHeader)
            ->getJson('/api/admin/id/' . $user_id);
        $response->assertStatus(200);
    }

    public function testAdminUsersOrdersShowExpectHttpUnauthenticated(): void
    {
        $user_id = $this->user->id;
        $response = $this->withHeaders($this->userHeader)
            ->getJson('/api/admin/id/' . $user_id);
        $response->assertStatus(401);
    }

    public function testAdminUpdateOrderExpectHttpOkAndUpdatedOrderWithId(): void
    {
        $order = $this->order->first();
        $good = Good::factory()->create();
        $request = [
            'items' => [
                [
                    'good_id' => $good->id,
                    'quantity' => 1,
                    'good_option_id' => null
                ]
            ]
        ];
        $response = $this->withHeaders($this->adminHeader)
            ->patchJson('/api/admin/update/' . $order->id, $request);
        $response->assertStatus(200)
            ->assertJson([
                'order' => [
                    'items' => [
                        [
                            'good_id' => $good->id,
                            'quantity' => 1,
                            'good_option_id' => null
                        ]
                    ]
                ]
            ]);
    }

    public function testAdminUpdateOrderExpectHttpInvalidData(): void
    {
        $order = $this->order->first();
        $good = Good::factory()->create();
        $request = [
            'items' => [
                [
                    'good_id' => $good->id,
                    'quantity' => 'test',
                    'good_option_id' => null
                ]
            ]
        ];
        $response = $this->withHeaders($this->adminHeader)
            ->patchJson('/api/admin/update/' . $order->id, $request);
        $response->assertStatus(422);
    }

    public function testAdminUpdateOrderExpectHttpUnauthenticated(): void
    {
        $order = $this->order->first();
        $good = Good::factory()->create();
        $request = [
            'items' => [
                [
                    'good_id' => $good->id,
                    'quantity' => 1,
                    'good_option_id' => null
                ]
            ]
        ];
        $response = $this->withHeaders($this->userHeader)
            ->patchJson('/api/admin/update/' . $order->id, $request);
        $response->assertStatus(401);
    }
}
