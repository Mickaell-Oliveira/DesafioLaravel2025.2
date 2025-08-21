<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userIds = User::where('type', 'user')->pluck('id')->toArray();

        if (count($userIds) < 2) {
             User::factory(10)->create();
             $userIds = User::where('type', 'user')->pluck('id')->toArray();
        }

        $buyerId = fake()->randomElement($userIds);

        $sellerId = fake()->randomElement(array_diff($userIds, [$buyerId]));

        return [
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'total' => fake()->randomFloat(2, 20, 1000),
        ];
    }
}