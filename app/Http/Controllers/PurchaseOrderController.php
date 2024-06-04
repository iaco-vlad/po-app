<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $searchTerm = $request->query('search', '');

        $purchaseOrders = PurchaseOrder::where('purchase_order_number', 'LIKE', "%{$searchTerm}%")
            ->orWhere('buyer_name', 'LIKE', "%{$searchTerm}%")
            ->latest('updated_at')
            ->paginate(10);
        return response()->json($purchaseOrders);
    }

    public function store(Request $request): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::create($request->except('items'));

        foreach ($request->input('items', []) as $item) {
            $purchaseOrder->items()->create($item);
        }

        return response()->json($purchaseOrder->load('items'), 201);
    }

    public function show($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);
        return response()->json($purchaseOrder);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->update($request->except('items'));

        $purchaseOrder->items()->delete();

        foreach ($request->input('items', []) as $item) {
            $purchaseOrder->items()->create($item);
        }

        return response()->json($purchaseOrder->load('items'));
    }

    public function destroy($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase order not found'], 404);
        }

        $purchaseOrder->delete();

        return response()->json(null, 204);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        PurchaseOrder::whereIn('id', $ids)->delete();
        return response()->json(null, 204);
    }
}
