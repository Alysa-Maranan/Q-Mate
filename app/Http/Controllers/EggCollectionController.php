<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EggCollection;
use Illuminate\Support\Facades\Validator;

class EggCollectionController extends Controller
{
    public function index()
    {
        try {
            $collections = EggCollection::orderBy('created_at', 'desc')->limit(50)->get();
            
            return response()->json([
                'success' => true,
                'collections' => $collections
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching collections: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cage_pen' => 'required|string',
            'total_eggs' => 'required|integer|min:0',
            'good_eggs' => 'required|integer|min:0',
            'cracked_eggs' => 'required|integer|min:0',
            'collection_time' => 'required|string',
            'egg_size' => 'nullable|string',
            'feed_brand' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $collection = EggCollection::create([
                'cage_pen' => $request->cage_pen,
                'total_eggs' => $request->total_eggs,
                'good_eggs' => $request->good_eggs,
                'cracked_eggs' => $request->cracked_eggs,
                'collection_time' => $request->collection_time,
                'notes' => $request->notes ?? "Egg Size: " . ($request->egg_size ?? 'mixed') . ", Feed Brand: " . ($request->feed_brand ?? 'Quail Layer Smash')
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Collection recorded successfully',
                'collection' => $collection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving collection: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $collection = EggCollection::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'collection' => $collection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found'
            ], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cage_pen' => 'required|string',
            'total_eggs' => 'required|integer|min:0',
            'good_eggs' => 'required|integer|min:0',
            'cracked_eggs' => 'required|integer|min:0',
            'collection_time' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $collection = EggCollection::findOrFail($id);
            
            $collection->update([
                'cage_pen' => $request->cage_pen,
                'total_eggs' => $request->total_eggs,
                'good_eggs' => $request->good_eggs,
                'cracked_eggs' => $request->cracked_eggs,
                'collection_time' => $request->collection_time,
                'notes' => $request->notes
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Collection updated successfully',
                'collection' => $collection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating collection: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $collection = EggCollection::findOrFail($id);
            $collection->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Collection deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting collection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/egg-collections
     * List collections with optional filters: ?date=Y-m-d or ?days=N
     */
    public function getCollections(Request $request)
    {
        try {
            $query = EggCollection::query();

            if ($request->filled('date')) {
                $query->whereDate('collection_time', $request->input('date'));
            } elseif ($request->filled('days')) {
                $days = max(1, min(365, (int) $request->input('days')));
                $query->where('collection_time', '>=', now()->subDays($days - 1)->startOfDay());
            }

            $collections = $query->orderBy('collection_time', 'desc')->limit(500)->get();

            return response()->json([
                'success' => true,
                'count' => $collections->count(),
                'collections' => $collections
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching collections: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/egg-collections/analytics?days=7
     * Daily egg production analytics with gap-filled day series.
     */
    public function getCollectionAnalytics(Request $request)
    {
        try {
            $days = (int) $request->input('days', 7);
            if ($days <= 0) {
                $days = 7;
            }
            if ($days > 90) {
                $days = 90;
            }

            $rows = EggCollection::query()
                ->where('collection_time', '>=', now()->subDays($days - 1)->startOfDay())
                ->selectRaw("DATE(collection_time) as day, COALESCE(SUM(total_eggs),0) as total_eggs, COALESCE(SUM(good_eggs),0) as good_eggs, COALESCE(SUM(cracked_eggs),0) as cracked_eggs")
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->keyBy(fn ($row) => date('Y-m-d', strtotime($row->day)));

            // Build a gap-free series so charts render continuous days
            $daily = [];
            $sumTotal = 0;
            $sumGood = 0;
            $sumCracked = 0;

            for ($i = $days - 1; $i >= 0; $i--) {
                $day = now()->subDays($i);
                $key = $day->toDateString();
                $row = $rows[$key] ?? null;

                $total = $row ? (int) $row->total_eggs : 0;
                $good = $row ? (int) $row->good_eggs : 0;
                $cracked = $row ? (int) $row->cracked_eggs : 0;

                $sumTotal += $total;
                $sumGood += $good;
                $sumCracked += $cracked;

                $daily[] = [
                    'date' => $key,
                    'label' => $day->format('M j'),
                    'total_eggs' => $total,
                    'good_eggs' => $good,
                    'cracked_eggs' => $cracked,
                ];
            }

            return response()->json([
                'success' => true,
                'days' => $days,
                'summary' => [
                    'total_eggs' => $sumTotal,
                    'good_eggs' => $sumGood,
                    'cracked_eggs' => $sumCracked,
                    'quality_rate' => $sumTotal > 0 ? round(($sumGood / $sumTotal) * 100, 1) : 0,
                    'daily_average' => round($sumTotal / $days, 1),
                ],
                'daily' => $daily
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error computing analytics: ' . $e->getMessage()
            ], 500);
        }
    }
}
