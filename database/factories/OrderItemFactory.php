<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $product = Product::inRandomOrder()->first();

        return [
            'order_id'   => Order::inRandomOrder()->first()->id, 
            'product_id' => $product->id,
            'seller_id'  => $product->user_id ?? 1,
            'quantity'   => $this->faker->numberBetween(1, 5),
            'price'      => $product->price,
        ];
    }
}
