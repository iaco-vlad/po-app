<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseOrderControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testIndex()
    {
        $purchaseOrders = PurchaseOrder::factory()->count(15)->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'purchase_order_number',
                        'buyer_name',
                        'total',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
            ])
            ->assertJsonCount(10, 'data');
    }

    public function testStore()
    {
        $purchaseOrderData = PurchaseOrder::factory()->make()->toArray();
        $itemsData = PurchaseOrderItem::factory()->count(3)->make()->toArray();

        $response = $this->postJson('/api/orders', array_merge($purchaseOrderData, ['items' => $itemsData]));

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'purchase_order_number',
                'buyer_name',
                'total',
                'created_at',
                'updated_at',
                'items' => [
                    '*' => [
                        'id',
                        'purchase_order_id',
                        'description',
                        'quantity',
                        'unit_price',
                        'category',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $purchaseOrderId = $response->json('id');

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrderId,
            ...$purchaseOrderData
        ]);

        foreach ($itemsData as $item) {
            $this->assertDatabaseHas('purchase_order_items', [
                'purchase_order_id' => $purchaseOrderId,
                ...$item
            ]);
        }
    }

    public function testShow()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $items = PurchaseOrderItem::factory()->count(3)->create(['purchase_order_id' => $purchaseOrder->id]);

        $response = $this->getJson("/api/orders/{$purchaseOrder->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'purchase_order_number',
                'buyer_name',
                'total',
                'created_at',
                'updated_at',
                'items' => [
                    '*' => [
                        'id',
                        'purchase_order_id',
                        'description',
                        'quantity',
                        'unit_price',
                        'category',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson([
                'id' => $purchaseOrder->id,
                'items' => $items->toArray(),
            ]);
    }

    public function testUpdate()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $items = PurchaseOrderItem::factory()->count(3)->create(['purchase_order_id' => $purchaseOrder->id]);

        $updatedPurchaseOrderData = PurchaseOrder::factory()->make()->toArray();
        $updatedItemsData = PurchaseOrderItem::factory()->count(2)->make()->toArray();

        foreach ($updatedItemsData as &$item) {
            $item['purchase_order_id'] = $purchaseOrder->id;
        }

        $response = $this->putJson("/api/orders/{$purchaseOrder->id}", array_merge($updatedPurchaseOrderData, ['items' => $updatedItemsData]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'purchase_order_number',
                'buyer_name',
                'total',
                'created_at',
                'updated_at',
                'items' => [
                    '*' => [
                        'id',
                        'purchase_order_id',
                        'description',
                        'quantity',
                        'unit_price',
                        'category',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson([
                'id' => $purchaseOrder->id,
                'purchase_order_number' => $updatedPurchaseOrderData['purchase_order_number'],
                'buyer_name' => $updatedPurchaseOrderData['buyer_name'],
                'total' => $updatedPurchaseOrderData['total'],
            ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            ...$updatedPurchaseOrderData
        ]);

        foreach ($updatedItemsData as $item) {
            $this->assertDatabaseHas('purchase_order_items', $item);
        }

        foreach ($items as $item) {
            $this->assertDatabaseMissing('purchase_order_items', $item->toArray());
        }
    }

    public function testDestroy()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $items = PurchaseOrderItem::factory()->count(3)->create(['purchase_order_id' => $purchaseOrder->id]);

        $response = $this->deleteJson("/api/orders/{$purchaseOrder->id}");

        $response->assertStatus(204)
            ->assertNoContent();

        $this->assertDatabaseMissing('purchase_orders', ['id' => $purchaseOrder->id]);
        $this->assertDatabaseMissing('purchase_order_items', ['purchase_order_id' => $purchaseOrder->id]);
    }

    public function testBulkDestroy()
    {
        $purchaseOrders = PurchaseOrder::factory()->count(5)->create();
        $purchaseOrderIds = $purchaseOrders->pluck('id')->toArray();

        foreach ($purchaseOrders as $purchaseOrder) {
            PurchaseOrderItem::factory()->count(3)->create(['purchase_order_id' => $purchaseOrder->id]);
        }

        $response = $this->deleteJson('/api/orders/bulk', ['ids' => $purchaseOrderIds]);

        $response->assertStatus(204)
            ->assertNoContent();

        $this->assertDatabaseMissing('purchase_orders', ['id' => $purchaseOrderIds]);
        $this->assertDatabaseMissing('purchase_order_items', ['purchase_order_id' => $purchaseOrderIds]);
    }

    public function testShowNotFound()
    {
        $response = $this->getJson('/api/orders/999');

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    public function testUpdateNotFound()
    {
        $updatedPurchaseOrderData = PurchaseOrder::factory()->make()->toArray();

        $response = $this->putJson('/api/orders/999', $updatedPurchaseOrderData);

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    public function testDestroyNotFound()
    {
        $response = $this->deleteJson('/api/orders/999');

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }
}
