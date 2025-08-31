<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Good;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoodControllerTest extends TestCase
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

        $this->admin = User::factory()->admin()->create();
        $this->adminToken = JWTAuth::fromUser($this->admin);
        $this->adminHeader = [
            'Authorization' => "Bearer $this->adminToken",
            'Accept' => 'application/json'
        ];
    }

    public function test_good_index_200(): void
    {
        $response = $this->getJson('/api/goods');

        $response->assertStatus(200);
    }

    public function test_good_show_200(): void
    {
        $good = Good::factory()->create();
        $response = $this->getJson("/api/goods/{$good->id}");
        $response->assertStatus(200);
    }

    public function test_good_show_404(): void
    {
        $response = $this->getJson("/api/goods/9999");
        $response->assertStatus(404);
    }

    public function test_good_store_200(): void
    {
        $good = [
            'name' => 'Test Good',
            'description' => 'Test Description',
            'price' => 100,
            'weight' => 1000,
            'category' => 'pizza',
        ];
        $response = $this->withHeaders($this->adminHeader)
            ->postJson('/api/goods', $good);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Test Good',
                'description' => 'Test Description',
            ]);
    }

    public function test_good_store_401(): void
    {
        $good = [
            'name' => 'Test Good',
            'description' => 'Test Description',
            'price' => 100,
            'weight' => 1000,
            'category' => 'pizza',
        ];
        $response = $this->withHeaders($this->userHeader)
            ->postJson('/api/goods', $good);

        $response->assertStatus(401);
    }

    public function test_good_update_200(): void
    {
        $good = Good::factory()->create();
        $id = $good->id;
        $request = [
            'name' => 'Test Good',
            'description' => 'Test Description',
            'price' => 100,
            'weight' => 1000,
            'category' => 'pizza',
        ];
        $response = $this->withHeaders($this->adminHeader)
            ->putJson("/api/goods/{$id}", $request);
        $response->assertStatus(200)
            ->assertJson($request);
    }

    public function test_good_update_401(): void
    {
        $good = Good::factory()->create();
        $id = $good->id;
        $request = [
            'name' => 'Test Good',
            'description' => 'Test Description',
            'price' => 100,
            'weight' => 1000,
            'category' => 'pizza',
        ];
        $response = $this->withHeaders($this->userHeader)
            ->putJson("/api/goods/{$id}", $request);
        $response->assertStatus(401);
    }

    public function test_good_destroy_200(): void
    {
        $good = Good::factory()->create();
        $id = $good->id;
        $response = $this->withHeaders($this->adminHeader)
            ->deleteJson("/api/goods/{$id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Deleted successfully']);
    }
}
