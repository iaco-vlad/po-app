<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PurchaseOrderMetricsController extends Controller
{
    public function index(): JsonResponse
    {
        $purchaseOrdersPerDay = PurchaseOrder::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $itemsPerCategory = DB::table('purchase_order_items')
            ->select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $data = [
            'purchaseOrdersPerDay' => $purchaseOrdersPerDay,
            'itemsPerCategory' => $itemsPerCategory,
        ];

        return response()->json($data);
    }
}
