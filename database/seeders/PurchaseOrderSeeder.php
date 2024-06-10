<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 35; $i++) {
            $createdAt = Carbon::now()->subDays(rand(0, 30));

            $purchaseOrder = PurchaseOrder::create([
                'purchase_order_number' => $faker->unique()->numberBetween(1000, 9999),
                'buyer_name' => $faker->name,
                'total' => $faker->randomFloat(2, 100, 10000),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $itemCount = $faker->numberBetween(1, 6);

            $total = 0;

            for ($j = 1; $j <= $itemCount; $j++) {
                $quantity = $faker->numberBetween(1, 10);
                $unitPrice = $faker->randomFloat(2, 10, 100);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'description' => $faker->sentence,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'category' => $faker->randomElement(['Category 1', 'Category 2', 'Category 3']),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $total += $quantity * $unitPrice;
            }

            $purchaseOrder->total = $total;
            $purchaseOrder->save();
        }
    }
}
