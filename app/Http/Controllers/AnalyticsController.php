<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EggCollection;
use App\Models\Sale;
use App\Models\ContactMessage;
use App\Models\FarmSetting;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function getAnalytics(Request $request)
    {
        try {
            $fromDate = $request->get('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));
            
            // Convert to Carbon instances
            $from = Carbon::parse($fromDate)->startOfDay();
            $to = Carbon::parse($toDate)->endOfDay();
            
            // Get egg collection data with error handling
            // NOTE: grouped by collection_time (the date the user recorded on the form)
            // so Analytics & Reports always match what the Collect & Inspect Eggs tab shows.
            $collections = EggCollection::whereBetween('collection_time', [$from, $to])->get();
            
            // Get orders data (from contact messages with ORDER: subject)
            $orders = ContactMessage::where('subject', 'LIKE', 'ORDER:%')
                ->whereBetween('created_at', [$from, $to])
                ->get();

            // Get recorded sales data from the sales table
            $sales = Sale::whereBetween('created_at', [$from, $to])->get();
            
            // Calculate metrics
            $totalEggsCollected = $collections->sum('total_eggs') ?? 0;
            $totalGoodEggs = $collections->sum('good_eggs') ?? 0;
            $totalCrackedEggs = $collections->sum('cracked_eggs') ?? 0;
            $qualityRate = $totalEggsCollected > 0 ? round(($totalGoodEggs / $totalEggsCollected) * 100, 1) : 0;
            
            $totalOrders = $orders->count();
            $completedOrders = $orders->where('status', 'done')->count();
            $pendingOrders = $totalOrders - $completedOrders;
            
            $activeDays = $collections->groupBy(function($item) {
                // Use collection_time as the primary date, fallback to created_at
                $date = $item->collection_time ?? $item->created_at;
                return $date ? Carbon::parse($date)->format('Y-m-d') : 'unknown';
            })->count();
            
            $avgOrdersPerDay = $activeDays > 0 ? round($totalOrders / $activeDays, 1) : 0;
            
            // Parse product sales from recorded sales entries
            $productSales = [
                'quail_eggs' => 0,
                'live_quail' => 0,
                'dressed_quail' => 0
            ];
            
            $productRevenue = [
                'quail_eggs' => 0,
                'live_quail' => 0,
                'dressed_quail' => 0
            ];
            
            $totalRevenue = 0;

            foreach ($sales as $sale) {
                $qty = (int) $sale->quantity;
                $price = (float) $sale->price;

                if ($sale->product_type === 'eggs') {
                    $productSales['quail_eggs'] += $qty;
                    $productRevenue['quail_eggs'] += $sale->total;
                } elseif ($sale->product_type === 'live_quail') {
                    $productSales['live_quail'] += $qty;
                    $productRevenue['live_quail'] += $sale->total;
                } elseif ($sale->product_type === 'dressed_quail') {
                    $productSales['dressed_quail'] += $qty;
                    $productRevenue['dressed_quail'] += $sale->total;
                }

                $totalRevenue += $sale->total;
            }

            $availableEggs = max(0, $totalGoodEggs - ($productSales['quail_eggs'] * 24));

            $liveQuails = (int) FarmSetting::get('live_quails', '0');
            $deadQuails = (int) FarmSetting::get('dead_quails', '0');
            $dressedQuails = (int) FarmSetting::get('dressed_quails_stock', '0');
            $totalRaised = $liveQuails + $deadQuails;
            $mortalityRate = $totalRaised > 0 ? round(($deadQuails / $totalRaised) * 100, 2) : 0;
            $survivalRate = $totalRaised > 0 ? round(100 - $mortalityRate, 2) : 100;
            $farmQuailStats = [
                'live_quails' => $liveQuails,
                'dead_quails' => $deadQuails,
                'dressed_quails' => $dressedQuails,
                'total_raised' => $totalRaised,
                'mortality_rate' => $mortalityRate,
                'survival_rate' => $survivalRate,
            ];
            
            // Daily data for charts
            $dailyOrders = [];
            $dailyCollections = [];
            $dailySales = [];
            
            // Group orders by date
            $ordersByDate = $orders->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });

            // Group sales by date
            $salesByDate = $sales->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });
            
            // Group collections by date
            $collectionsByDate = $collections->groupBy(function($item) {
                // Use collection_time as the primary date, fallback to created_at
                $date = $item->collection_time ?? $item->created_at;
                return $date ? Carbon::parse($date)->format('Y-m-d') : 'unknown';
            });
            
            // Create daily data arrays
            $currentDate = $from->copy();
            while ($currentDate->lessThanOrEqualTo($to)) {
                $dateStr = $currentDate->format('Y-m-d');
                $dateDisplay = $currentDate->format('M d');
                
                $dayOrders = $ordersByDate->get($dateStr, collect());
                $dayCollections = $collectionsByDate->get($dateStr, collect());
                $daySales = $salesByDate->get($dateStr, collect());
                
                $dailyOrders[] = [
                    'date' => $dateDisplay,
                    'orders' => $dayOrders->count(),
                    'completed' => $dayOrders->where('status', 'done')->count()
                ];
                
                $dailyCollections[] = [
                    'date' => $dateDisplay,
                    'total_eggs' => $dayCollections->sum('total_eggs') ?? 0,
                    'good_eggs' => $dayCollections->sum('good_eggs') ?? 0,
                    'cracked_eggs' => $dayCollections->sum('cracked_eggs') ?? 0
                ];

                $dailySales[] = [
                    'date' => $dateDisplay,
                    'sales' => $daySales->count(),
                    'revenue' => $daySales->sum('total')
                ];
                
                $currentDate->addDay();
            }
            
            // Determine best product
            $bestProduct = 'Quail Eggs';
            $maxRevenue = $productRevenue['quail_eggs'];
            if ($productRevenue['live_quail'] > $maxRevenue) {
                $bestProduct = 'Live Quail';
                $maxRevenue = $productRevenue['live_quail'];
            }
            if ($productRevenue['dressed_quail'] > $maxRevenue) {
                $bestProduct = 'Dressed Quail';
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_eggs_collected' => $totalEggsCollected,
                    'total_good_eggs' => $totalGoodEggs,
                    'total_cracked_eggs' => $totalCrackedEggs,
                    'quality_rate' => $qualityRate,
                    'total_orders' => $totalOrders,
                    'completed_orders' => $completedOrders,
                    'pending_orders' => $pendingOrders,
                    'active_days' => $activeDays,
                    'avg_orders_per_day' => $avgOrdersPerDay,
                    'product_sales' => $productSales,
                    'product_revenue' => $productRevenue,
                    'total_revenue' => $totalRevenue,
                    'best_product' => $bestProduct,
                    'available_eggs' => $availableEggs,
                    'farm_quail_stats' => $farmQuailStats,
                    'daily_orders' => $dailyOrders,
                    'daily_collections' => $dailyCollections,
                    'daily_sales' => $dailySales,
                    'sales_count' => $sales->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Analytics Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching analytics data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
