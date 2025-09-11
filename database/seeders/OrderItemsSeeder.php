<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderItemsSeeder extends Seeder
{
    public function run()
    {
        Order::all()->each(function ($order) {
            OrderItem::factory()->count(rand(1,5))->create([
                'order_id' => $order->id
            ]);
        });
    }
}
