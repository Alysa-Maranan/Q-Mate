<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\FarmSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_type' => 'required|in:eggs,live_quail,dressed_quail',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255'
        ]);

        $total = $request->quantity * $request->price;

        $liveQuails = (int) FarmSetting::get('live_quails', '0');
        $dressedQuailsStock = (int) FarmSetting::get('dressed_quails_stock', '0');

        if ($request->product_type === 'live_quail' && $request->quantity > $liveQuails) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough live quails in stock.',
            ], 422);
        }

        if ($request->product_type === 'dressed_quail' && $request->quantity > $dressedQuailsStock) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough dressed quails in stock.',
            ], 422);
        }

        $sale = DB::transaction(function () use ($request, $total, $liveQuails, $dressedQuailsStock) {
            if ($request->product_type === 'live_quail') {
                FarmSetting::set('live_quails', (string) max(0, $liveQuails - (int) $request->quantity));
            }

            if ($request->product_type === 'dressed_quail') {
                FarmSetting::set('dressed_quails_stock', (string) max(0, $dressedQuailsStock - (int) $request->quantity));
            }

            return Sale::create([
                'product_type' => $request->product_type,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total' => $total,
                'customer_name' => $request->customer_name,
                'notes' => $request->notes
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Sale recorded successfully',
            'sale' => $sale,
            'stock' => [
                'live_quails' => (int) FarmSetting::get('live_quails', '0'),
                'dressed_quails_stock' => (int) FarmSetting::get('dressed_quails_stock', '0'),
            ]
        ]);
    }

    public function getSales(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        $sales = Sale::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'sales' => $sales
        ]);
    }

    public function getSalesAnalytics(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        $sales = Sale::whereBetween('created_at', [$from, $to])->get();

        // Calculate totals
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');

        // Product breakdown
        $productSales = [
            'eggs' => $sales->where('product_type', 'eggs')->sum('quantity'),
            'live_quail' => $sales->where('product_type', 'live_quail')->sum('quantity'),
            'dressed_quail' => $sales->where('product_type', 'dressed_quail')->sum('quantity'),
        ];

        $productRevenue = [
            'eggs' => $sales->where('product_type', 'eggs')->sum('total'),
            'live_quail' => $sales->where('product_type', 'live_quail')->sum('total'),
            'dressed_quail' => $sales->where('product_type', 'dressed_quail')->sum('total'),
        ];

        // Daily breakdown
        $dailySales = [];
        for ($date = $from->copy(); $date <= $to; $date->addDay()) {
            $daySales = $sales->whereBetween('created_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay()
            ]);

            $dailySales[] = [
                'date' => $date->format('M d'),
                'sales' => $daySales->count(),
                'revenue' => $daySales->sum('total')
            ];
        }

        // Best selling product
        $bestProduct = 'Quail Eggs';
        $maxRevenue = $productRevenue['eggs'];
        if ($productRevenue['live_quail'] > $maxRevenue) {
            $bestProduct = 'Live Quail';
            $maxRevenue = $productRevenue['live_quail'];
        }
        if ($productRevenue['dressed_quail'] > $maxRevenue) {
            $bestProduct = 'Dressed Quail';
        }

        // Calculate averages
        $daysDiff = max(1, $from->diffInDays($to) + 1);
        $avgSalesPerDay = round($totalSales / $daysDiff, 1);
        $avgRevenuePerDay = round($totalRevenue / $daysDiff, 2);

        return response()->json([
            'success' => true,
            'data' => [
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'best_product' => $bestProduct,
                'avg_sales_per_day' => $avgSalesPerDay,
                'avg_revenue_per_day' => $avgRevenuePerDay,
                'product_sales' => $productSales,
                'product_revenue' => $productRevenue,
                'daily_sales' => $dailySales,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ]
        ]);
    }

    /**
     * Delete a recorded sale.
     * DELETE /api/sales/{id}
     *
     * Removes the row from the database (so Analytics & Reports update too)
     * and restores the stock that was deducted when this sale was recorded.
     */
    public function destroy(Request $request, $id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Sale not found',
            ], 404);
        }

        $updatedStock = DB::transaction(function () use ($sale) {
            // Restore the stock deducted when this sale was recorded
            if ($sale->product_type === 'live_quail') {
                $liveQuails = (int) FarmSetting::get('live_quails', '0');
                FarmSetting::set('live_quails', (string) ($liveQuails + (int) $sale->quantity));
            }

            if ($sale->product_type === 'dressed_quail') {
                $dressedQuailsStock = (int) FarmSetting::get('dressed_quails_stock', '0');
                FarmSetting::set('dressed_quails_stock', (string) ($dressedQuailsStock + (int) $sale->quantity));
            }

            $sale->delete();

            return [
                'live_quails'          => (int) FarmSetting::get('live_quails', '0'),
                'dressed_quails_stock' => (int) FarmSetting::get('dressed_quails_stock', '0'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Sale deleted successfully',
            'stock'   => $updatedStock,
        ]);
    }
}