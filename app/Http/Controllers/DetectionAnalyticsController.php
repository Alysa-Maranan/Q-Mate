<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetectionHistory;
use App\Models\FeedDetection;
use App\Models\QuailBreed;
use Barryvdh\DomPDF\Facade\Pdf;

class DetectionAnalyticsController extends Controller
{
    /**
     * Display detection analytics dashboard
     */
    public function index(Request $request)
    {
        if (!auth()->check()) return redirect('/login');

        $userId = auth()->id();
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        try {
            // Quail Detection Stats
            $totalDetections = DetectionHistory::where('user_id', $userId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $successfulDetections = DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'quail')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $humanDetections = DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'human')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $unknownDetections = DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'unknown')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $avgConfidence = DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'quail')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->avg('confidence');

            // Breed Distribution
            $breedDistribution = DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'quail')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->with('breed')
                ->selectRaw('quail_breed_id, COUNT(*) as count')
                ->groupBy('quail_breed_id')
                ->get();

            // Top Detected Locations
            $topLocations = DetectionHistory::where('user_id', $userId)
                ->where('location', '!=', null)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('location, COUNT(*) as count')
                ->groupBy('location')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            // Feed Detection Stats
            $feedCompatible = FeedDetection::where('user_id', $userId)
                ->where('compatibility_status', 'suitable')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $feedCaution = FeedDetection::where('user_id', $userId)
                ->where('compatibility_status', 'caution')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $feedIncompatible = FeedDetection::where('user_id', $userId)
                ->where('compatibility_status', 'unsuitable')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            // Recent Detections
            $recentDetections = DetectionHistory::where('user_id', $userId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->with('breed')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        } catch (\Exception $e) {
            // Handle table not found or other errors gracefully
            \Log::error('Analytics query error: ' . $e->getMessage());
            
            $totalDetections = 0;
            $successfulDetections = 0;
            $humanDetections = 0;
            $unknownDetections = 0;
            $avgConfidence = 0;
            $breedDistribution = collect([]);
            $topLocations = collect([]);
            $feedCompatible = 0;
            $feedCaution = 0;
            $feedIncompatible = 0;
            $recentDetections = collect([]);
        }

        $data = compact(
            'totalDetections', 'successfulDetections', 'humanDetections', 'unknownDetections',
            'avgConfidence', 'breedDistribution', 'topLocations',
            'feedCompatible', 'feedCaution', 'feedIncompatible',
            'recentDetections', 'dateFrom', 'dateTo'
        );

        return view('detection-analytics', $data);
    }

    /**
     * Export detection history as PDF
     */
    public function exportPdf(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userId = auth()->id();
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $detections = DetectionHistory::where('user_id', $userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('breed')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $detections->count(),
            'quail' => $detections->where('detection_type', 'quail')->count(),
            'human' => $detections->where('detection_type', 'human')->count(),
            'unknown' => $detections->where('detection_type', 'unknown')->count(),
            'avgConfidence' => $detections->where('detection_type', 'quail')->avg('confidence'),
        ];

        $pdf = Pdf::loadView('exports.detection-pdf', compact('detections', 'stats', 'dateFrom', 'dateTo'));
        return $pdf->download('detection-history-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export detection history as CSV
     */
    public function exportCsv(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userId = auth()->id();
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $detections = DetectionHistory::where('user_id', $userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('breed')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'detection-history-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename"
        ];

        $callback = function () use ($detections) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['Date', 'Type', 'Breed', 'Confidence', 'Location', 'Notes']);

            // Write data
            foreach ($detections as $detection) {
                fputcsv($file, [
                    $detection->created_at->format('Y-m-d H:i:s'),
                    $detection->detection_type,
                    $detection->breed?->name ?? 'N/A',
                    $detection->confidence ? ($detection->confidence * 100) . '%' : 'N/A',
                    $detection->location ?? 'N/A',
                    $detection->detection_notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export feed detection compatibility as CSV
     */
    public function exportFeedCsv(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userId = auth()->id();
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $detections = FeedDetection::where('user_id', $userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['breed', 'feedItem'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'feed-detection-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename"
        ];

        $callback = function () use ($detections) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['Date', 'Breed', 'Feed Item', 'Compatibility', 'Confidence', 'Notes']);

            // Write data
            foreach ($detections as $detection) {
                fputcsv($file, [
                    $detection->created_at->format('Y-m-d H:i:s'),
                    $detection->breed?->name ?? 'N/A',
                    $detection->feedItem?->name ?? $detection->feed_name ?? 'Unknown',
                    $detection->compatibility_status,
                    $detection->confidence ? ($detection->confidence * 100) . '%' : 'N/A',
                    $detection->compatibility_notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
