<?php

namespace Database\Seeders;

use App\Models\Good;
use App\Models\GoodOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $pizzas = [
            [
                'name' => 'Margarita',
                'description' => 'Margarita - pizza with tomato sauce and tomato',
                'price' => 500,
                'weight' => 700,
                'category' => 'pizza',
            ],
            [
                'name' => 'Mamasita',
                'description' => 'Mamasita - pizza with sausages',
                'price' => 650,
                'weight' => 600,
                'category' => 'pizza',
            ]
        ];
        $drinks = [
            [
                'name' => 'Coca-cola',
                'description' => 'Coca-cola - drink with coca',
                'price' => 100,
                'weight' => 250,
                'category' => 'drink',
            ],
            [
                'name' => 'Beer "Homebrew"',
                'description' => 'Beer "Homebrew" - liquid bread',
                'price' => 200,
                'weight' => 500,
                'category' => 'drink',
            ]
        ];

        foreach (array_merge($pizzas, $drinks) as $good) {
            Good::create($good);
        }

        $pizzaOptions = [
            ['type' => 'dough', 'name' => 'standart dough', 'price' => 0],
            ['type' => 'dough', 'name' => 'thick dough', 'price' => 100],
            ['type' => 'board', 'name' => 'standart board', 'price' => 0],
            ['type' => 'board', 'name' => 'сheesy board', 'price' => 150],
        ];

        $pizzas = Good::where('category', 'pizza')->get();
        foreach ($pizzas as $pizza) {
            foreach ($pizzaOptions as $option) {
                GoodOption::create(array_merge($option, ['good_id' => $pizza->id]));
            }
        }

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Adminov',
            'phone_number' => '+79999999999',
            'password' => Hash::make('password'),
            'birthday' => '1990-01-01',
            'admin' => 'yes'
        ]);

        User::factory()->admin()->create();

        $users = User::factory()
            ->count(50)->create();

        $users->each(function ($user) {
            UserAddress::factory()
                ->count(rand(1,3))->create([
                    'user_id' => $user->id
                ]);
        });

        $orders = collect([]);
        $users->each(function ($user) use (&$orders) {
            $userOrder = Order::factory()
                ->count(rand(1, 3))->create([
                    'user_id' => $user->id,
                    'user_address_id' => $user->addresses->random()->id
                ]);
            $orders = $orders->merge($userOrder);
        });

        $users->each(function ($order) {
            OrderItem::factory()
                ->count(rand(1,5))->create([
                    'order_id' => $order->id
                ]);
        });
    }
}
