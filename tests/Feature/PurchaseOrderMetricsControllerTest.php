<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderMetricsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider purchaseOrdersPerDayDataProvider
     */
    public function testPurchaseOrdersPerDay($purchaseOrderData, $expectedResult)
    {
        foreach ($purchaseOrderData as $data) {
            PurchaseOrder::factory()->create($data);
        }

        $response = $this->getJson('/api/metrics/purchase-orders');

        $response->assertStatus(200)
            ->assertJsonFragment(['purchaseOrdersPerDay' => $expectedResult]);
    }

    /**
     * @dataProvider itemsPerCategoryDataProvider
     */
    public function testItemsPerCategory($itemData, $expectedResult)
    {
        foreach ($itemData as $data) {
            $purchaseOrder = PurchaseOrder::factory()->create();
            PurchaseOrderItem::factory()->create(array_merge($data, ['purchase_order_id' => $purchaseOrder->id]));
        }

        $response = $this->getJson('/api/metrics/purchase-orders');

        $response->assertStatus(200)
            ->assertJsonFragment(['itemsPerCategory' => $expectedResult]);
    }

    public static function purchaseOrdersPerDayDataProvider()
    {
        return [
            'no_purchase_orders' => [
                'purchaseOrderData' => [],
                'expectedResult' => [],
            ],
            'single_purchase_order' => [
                'purchaseOrderData' => [
                    ['created_at' => '2023-06-01 12:00:00'],
                ],
                'expectedResult' => ['2023-06-01' => 1],
            ],
            'multiple_purchase_orders' => [
                'purchaseOrderData' => [
                    ['created_at' => '2023-06-01 12:00:00'],
                    ['created_at' => '2023-06-01 14:00:00'],
                    ['created_at' => '2023-06-02 10:00:00'],
                    ['created_at' => '2023-06-02 16:00:00'],
                    ['created_at' => '2023-06-02 18:00:00'],
                ],
                'expectedResult' => [
                    '2023-06-01' => 2,
                    '2023-06-02' => 3,
                ],
            ],
        ];
    }

    public static function itemsPerCategoryDataProvider()
    {
        return [
            'no_items' => [
                'itemData' => [],
                'expectedResult' => [],
            ],
            'single_item' => [
                'itemData' => [
                    ['category' => 'Category 1'],
                ],
                'expectedResult' => ['Category 1' => 1],
            ],
            'multiple_items' => [
                'itemData' => [
                    ['category' => 'Category 1'],
                    ['category' => 'Category 1'],
                    ['category' => 'Category 2'],
                    ['category' => 'Category 2'],
                    ['category' => 'Category 3'],
                ],
                'expectedResult' => [
                    'Category 1' => 2,
                    'Category 2' => 2,
                    'Category 3' => 1,
                ],
            ],
        ];
    }
}
