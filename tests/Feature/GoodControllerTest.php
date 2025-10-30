<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        $this->userToken = $this->user->createToken('test-token')->plainTextToken;
        $this->userHeader = [
            'Authorization' => "Bearer $this->userToken",
            'Accept' => 'application/json'
        ];

        $this->admin = User::factory()->admin()->create();
        $this->adminToken = $this->admin->createToken('admin-token')->plainTextToken;
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
}
