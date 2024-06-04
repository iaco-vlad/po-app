<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition()
    {
        return [
            'purchase_order_number' => $this->faker->unique()->randomNumber(8),
            'buyer_name' => $this->faker->name,
            'total' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
