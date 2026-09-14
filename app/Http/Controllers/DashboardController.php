<?php

namespace App\Http\Controllers;

use App\Helpers\QuailBreedHelper;
use App\Models\QuailBreed;
use App\Models\FarmSetting;
use App\Models\SensorReading;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Product;
use App\Models\FeedHistory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currentQuailBreed = QuailBreedHelper::getCurrentBreed();
        $currentBreeds = QuailBreedHelper::getCurrentBreeds();
        $allBreeds = QuailBreed::getActive();
        
        // Egg collection metrics for Today's Summary
        $todayTotalEggs = 0;
        $todayGoodEggs = 0;
        $todayCrackedEggs = 0;

        $today = now()->toDateString();

        $todayRow = \App\Models\EggCollection::query()
            ->whereDate('collection_time', $today)
            ->selectRaw('COALESCE(SUM(total_eggs),0) as total_eggs, COALESCE(SUM(good_eggs),0) as good_eggs, COALESCE(SUM(cracked_eggs),0) as cracked_eggs')
            ->first();

        if ($todayRow) {
            $todayTotalEggs = (int) ($todayRow->total_eggs ?? 0);
            $todayGoodEggs = (int) ($todayRow->good_eggs ?? 0);
            $todayCrackedEggs = (int) ($todayRow->cracked_eggs ?? 0);
        }

        $farm = [
            'live_quails' => FarmSetting::get('live_quails', 0),
            'dead_quails' => FarmSetting::get('dead_quails', 0),
            'dressed_quails_stock' => FarmSetting::get('dressed_quails_stock', 0),
            'quail_count_date' => FarmSetting::get('quail_count_date', date('Y-m-d')),
            'farm_name' => FarmSetting::get('farm_name', "Escalona's Farm") ?: "Escalona's Farm",
            'farm_address' => FarmSetting::get('farm_address', 'Pagkakaisa, Naujan, Or. Mindoro') ?: 'Pagkakaisa, Naujan, Or. Mindoro',
            'farm_phone' => FarmSetting::get('farm_phone', '+63 917 123 4567') ?: '+63 917 123 4567',
            'farm_email' => FarmSetting::get('farm_email', 'escalona.farm@gmail.com') ?: 'escalona.farm@gmail.com',

            // Keys expected by resources/views/dashboard.blade.php
            'total_eggs_collected' => $todayTotalEggs,
            'good_eggs' => $todayGoodEggs,
            'cracked_eggs' => $todayCrackedEggs,
        ];

        // ── Live sensor reading (Temperature & Humidity) ──
        $latestReading = SensorReading::latestReading()->first();

        // ── Sales & Orders metrics ──
        $salesSummary = $this->getSalesSummary($today);
        $ordersSummary = $this->getOrdersSummary($today);

        // ── Real inventory status computed from product stock ──
        $inventoryStatus = $this->getInventoryStatus();

        // ── Feeding activity summary ──
        $feedInfo = $this->getFeedInfo($today);

        // ── Egg production trend (last 7 days) ──
        $eggTrend = $this->getEggTrend();

        return view('dashboard', compact(
            'currentQuailBreed',
            'currentBreeds',
            'allBreeds',
            'farm',
            'latestReading',
            'salesSummary',
            'ordersSummary',
            'inventoryStatus',
            'feedInfo',
            'eggTrend'
        ));
    }

    /**
     * Live summary API endpoint polled by the dashboard every few seconds
     * so all cards stay up to date without a page reload.
     */
    public function summary()
    {
        $today = now()->toDateString();

        $todayRow = \App\Models\EggCollection::query()
            ->whereDate('collection_time', $today)
            ->selectRaw('COALESCE(SUM(total_eggs),0) as total_eggs, COALESCE(SUM(good_eggs),0) as good_eggs, COALESCE(SUM(cracked_eggs),0) as cracked_eggs')
            ->first();

        $latestReading = SensorReading::latestReading()->first();
        $isFresh = $latestReading
            && $latestReading->recorded_at
            && $latestReading->recorded_at->gt(now()->subMinutes(60));

        return response()->json([
            'eggs' => [
                'total' => (int) ($todayRow->total_eggs ?? 0),
                'good' => (int) ($todayRow->good_eggs ?? 0),
                'cracked' => (int) ($todayRow->cracked_eggs ?? 0),
            ],
            'sales' => $this->getSalesSummary($today),
            'orders' => $this->getOrdersSummary($today),
            'sensor' => [
                'temperature' => ($isFresh && !is_null($latestReading->temperature)) ? round((float) $latestReading->temperature, 1) : null,
                'humidity' => ($isFresh && !is_null($latestReading->humidity)) ? round((float) $latestReading->humidity, 1) : null,
                'recorded_at' => $isFresh ? $latestReading->recorded_at->format('g:i A') : null,
            ],
            'inventory' => $this->getInventoryStatus(),
            'feed' => $this->getFeedInfo($today),
        ]);
    }

    private function getSalesSummary(string $today): array
    {
        $todayRow = Sale::whereDate('created_at', $today)
            ->selectRaw('COALESCE(SUM(total),0) as revenue, COUNT(*) as cnt')
            ->first();

        $allRow = Sale::selectRaw('COALESCE(SUM(total),0) as revenue, COUNT(*) as cnt')->first();

        return [
            'today_revenue' => (float) ($todayRow->revenue ?? 0),
            'today_count' => (int) ($todayRow->cnt ?? 0),
            'total_revenue' => (float) ($allRow->revenue ?? 0),
            'total_count' => (int) ($allRow->cnt ?? 0),
        ];
    }

    private function getOrdersSummary(string $today): array
    {
        $counts = Order::selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $get = fn ($key) => (int) ($counts[$key] ?? 0);

        return [
            'today' => Order::whereDate('created_at', $today)->count(),
            'pending' => $get('pending'),
            'confirmed' => $get('confirmed'),
            'processing' => $get('processing'),
            'to_ship' => $get('to_ship'),
            'ready' => $get('ready'),
            'completed' => $get('completed'),
            'cancelled' => $get('cancelled'),
        ];
    }

    private function getInventoryStatus(): array
    {
        $products = Product::where('is_active', true)->orderBy('name')->get(['name', 'stock', 'unit']);

        $outOfStock = $products->filter(fn ($p) => (int) $p->stock <= 0);
        $lowStock = $products->filter(fn ($p) => (int) $p->stock > 0 && (int) $p->stock <= 10);

        $describe = function ($items) {
            $names = $items->take(2)->map(fn ($p) => $p->name . ' (' . (int) $p->stock . ' ' . $p->unit . ')')->implode(', ');

            return $items->count() > 2 ? $names . ' +' . ($items->count() - 2) . ' more' : $names;
        };

        if ($outOfStock->isNotEmpty()) {
            $level = 'out';
            $label = 'Out of Stock';
            $detail = $describe($outOfStock);
        } elseif ($lowStock->isNotEmpty()) {
            $level = 'low';
            $label = 'Low Stock';
            $detail = $describe($lowStock);
        } else {
            $level = 'ok';
            $label = 'On Track';
            $detail = $products->isEmpty() ? 'No active products yet' : 'All products sufficiently stocked';
        }

        return [
            'level' => $level,
            'label' => $label,
            'detail' => $detail,
            'low_stock' => $lowStock->values()->map(fn ($p) => [
                'name' => $p->name,
                'stock' => (int) $p->stock,
                'unit' => $p->unit,
            ])->toArray(),
            'out_of_stock' => $outOfStock->values()->map(fn ($p) => [
                'name' => $p->name,
                'stock' => (int) $p->stock,
                'unit' => $p->unit,
            ])->toArray(),
        ];
    }

    private function getFeedInfo(string $today): array
    {
        $lastFeed = FeedHistory::orderBy('fed_at', 'desc')->first();
        $feedsToday = FeedHistory::whereDate('fed_at', $today)->count();

        return [
            'feeds_today' => $feedsToday,
            'last_fed_at' => ($lastFeed && $lastFeed->fed_at) ? $lastFeed->fed_at->format('M d, g:i A') : null,
            'last_cage' => $lastFeed?->cage_number,
        ];
    }

    private function getEggTrend(): array
    {
        $start = now()->subDays(6)->startOfDay();

        $rows = \App\Models\EggCollection::query()
            ->where('collection_time', '>=', $start)
            ->selectRaw('DATE(collection_time) as day, COALESCE(SUM(total_eggs),0) as total_eggs, COALESCE(SUM(good_eggs),0) as good_eggs')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy(fn ($row) => date('Y-m-d', strtotime($row->day)));

        $labels = [];
        $totals = [];
        $good = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $key = $day->toDateString();

            $labels[] = $day->format('D, M j');
            $totals[] = isset($rows[$key]) ? (int) $rows[$key]->total_eggs : 0;
            $good[] = isset($rows[$key]) ? (int) $rows[$key]->good_eggs : 0;
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
            'good' => $good,
        ];
    }
}