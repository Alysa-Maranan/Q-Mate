<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\QuailBreedHelper;
use App\Models\DetectionHistory;
use App\Models\FeedDetection;
use App\Models\FeedItem;
use App\Models\QuailBreed;

class QuailDetectionController extends Controller
{
    /**
     * Display the quail detection interface
     */
    public function index()
    {
        if (!auth()->check()) return redirect('/login');
        
        $currentQuailBreed = QuailBreedHelper::getCurrentBreed();

        // Only the 3 supported quail breeds
        $quailBreeds = QuailBreed::whereIn('id', [1, 2, 3])->get();
        
        try {
            $recentDetections = DetectionHistory::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $recentDetections = collect([]);
        }
        
        return view('quail-detection', compact(
            'currentQuailBreed',
            'quailBreeds',
            'recentDetections'
        ));
    }

    /**
     * API endpoint to save detection results
     */
    public function saveDetection(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'detection_type' => 'required|in:quail,human,unknown',
            'quail_breed_id' => 'nullable|exists:quail_breeds,id',
            'confidence' => 'nullable|numeric|min:0|max:1',
            'location' => 'nullable|string',
            'detection_notes' => 'nullable|string',
            'image_data' => 'nullable|string'
        ]);

        try {
            $imagePath = null;
            
            if ($request->has('image_data')) {
                $imageData = $request->input('image_data');

                if (strpos($imageData, 'base64') !== false) {
                    $image = str_replace('data:image/jpeg;base64,', '', $imageData);
                    $filename = 'detection_' . auth()->id() . '_' . time() . '_' . uniqid() . '.jpg';
                    $path = storage_path('app/public/detections/' . $filename);
                    
                    if (!is_dir(dirname($path))) {
                        mkdir(dirname($path), 0755, true);
                    }
                    
                    file_put_contents($path, base64_decode($image));
                    $imagePath = 'detections/' . $filename;
                }
            }

            $detection = DetectionHistory::create([
                'user_id' => auth()->id(),
                'quail_breed_id' => $validated['quail_breed_id'] ?? null,
                'detection_type' => $validated['detection_type'],
                'confidence' => $validated['confidence'] ?? null,
                'location' => $validated['location'] ?? null,
                'detection_notes' => $validated['detection_notes'] ?? null,
                'image_path' => $imagePath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detection recorded successfully',
                'detection_id' => $detection->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Quail detection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error saving detection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quail breed information with enhanced details
     */
    public function getBreedInfo($breedId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Only allow the 3 supported breeds
        if (!in_array((int) $breedId, [1, 2, 3], true)) {
            return response()->json(['error' => 'Breed not found'], 404);
        }

        $breed = QuailBreed::whereIn('id', [1, 2, 3])->find($breedId);
        
        if (!$breed) {
            return response()->json(['error' => 'Breed not found'], 404);
        }

        $compatibleFeeds = FeedItem::all()
            ->filter(function ($feed) use ($breedId) {
                return $feed->isSuitableForBreed($breedId) !== false;
            })
            ->values();

        return response()->json([
            'success' => true,
            'breed' => [
                'id' => $breed->id,
                'name' => $breed->name,
                'scientific_name' => $breed->scientific_name,
                'description' => $breed->description,
                'color_markings' => $breed->color_markings,
                'size_category' => $breed->size_category,
                'egg_production_rate' => $breed->egg_production_rate,
                'mature_weight' => $breed->mature_weight,
                'maturity_age' => $breed->maturity_age,
                'optimal_temperature' => $breed->optimal_temperature,
                'optimal_humidity' => $breed->optimal_humidity,
                'care_requirements' => $breed->care_requirements,
                'recommended_feeds' => $breed->recommended_feeds,
                'common_diseases' => $breed->common_diseases,
                'image_url' => $breed->image_url,
            ],
            'compatible_feeds' => $compatibleFeeds->map(fn($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'category' => $f->category
            ])->toArray()
        ]);
    }

    /**
     * Get detection history
     */
    public function getHistory(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = DetectionHistory::where('user_id', auth()->id())
            ->with('breed');

        if ($request->has('type')) {
            $query->where('detection_type', $request->input('type'));
        }

        if ($request->has('breed_id')) {
            $query->where('quail_breed_id', $request->input('breed_id'));
        }

        $limit = $request->input('limit', 50);

        $detections = $query->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $detections
        ]);
    }

    /**
     * Get detection statistics
     */
    public function getStats(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $stats = [
            'total_detections' => DetectionHistory::where('user_id', $userId)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),

            'successful_detections' => DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'quail')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),

            'human_detections' => DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'human')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),

            'average_confidence' => DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'quail')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->avg('confidence'),

            'breeds_detected' => DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'quail')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->distinct('quail_breed_id')
                ->count(),

            'by_breed' => DetectionHistory::where('user_id', $userId)
                ->where('detection_type', 'quail')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('quail_breed_id, COUNT(*) as count')
                ->groupBy('quail_breed_id')
                ->with('breed')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Classify quail breed from image using trained ML model
     */
    public function classifyBreed(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        try {
            $imagePath = $request->file('image')->store('temp/classifications', 'local');
            $fullPath = storage_path('app/' . $imagePath);

            \Log::info(
                'Classification request: Image saved to ' . $fullPath .
                ', file exists: ' . (file_exists($fullPath) ? 'yes' : 'no')
            );

            $pythonScript = base_path('classify_quail_breed.py');
            $modelPath = base_path('public/models/quail_breed_model.keras');
            $metadataPath = base_path('public/models/model_metadata.json');

            \Log::info('Paths check:', [
                'pythonScript exists' => file_exists($pythonScript),
                'modelPath exists' => file_exists($modelPath),
                'metadataPath exists' => file_exists($metadataPath)
            ]);

            if (!file_exists($pythonScript)) {
                \Log::error('Python script not found: ' . $pythonScript);
                return $this->getRandomBreedResponse('Python classifier not available');
            }

            $classifierUrl = env(
                'QUAIL_CLASSIFIER_URL',
                'http://127.0.0.1:8000/classify'
            );

            $result = null;

            try {
                \Log::info('Calling classifier service at: ' . $classifierUrl);

                $response = Http::timeout(60)
                    ->attach(
                        'image',
                        fopen($fullPath, 'r'),
                        basename($fullPath)
                    )
                    ->post($classifierUrl);

                \Log::info(
                    'Classifier service response status: ' .
                    $response->status()
                );

                if ($response->ok()) {
                    $result = $response->json();

                    \Log::info(
                        'Classifier service result: ' .
                        substr(json_encode($result), 0, 500)
                    );
                } else {
                    \Log::error(
                        'Classifier service returned non-OK: ' .
                        $response->body()
                    );
                }
            } catch (\Exception $e) {
                \Log::error(
                    'Classifier HTTP error: ' .
                    $e->getMessage()
                );
            }

            if (!$result) {
                $command = sprintf(
                    'python "%s" "%s" "%s" "%s" 2>&1',
                    $pythonScript,
                    $fullPath,
                    $modelPath,
                    $metadataPath
                );

                \Log::info(
                    'Executing fallback command: ' .
                    $command
                );

                $output = shell_exec($command);

                \Log::info(
                    'Python fallback output: ' .
                    substr($output, 0, 500)
                );

                $result = json_decode($output, true);
            }

            @unlink($fullPath);

            if (!$result || !isset($result['predicted_class'])) {
                \Log::warning(
                    'Classification returned no predicted_class. Using fallback.'
                );

                return $this->getRandomBreedResponse(
                    'Classification fallback'
                );
            }

            $breedMapping = [
                0 => 1,
                1 => 2,
                2 => 3
            ];

            $classIndex = $result['predicted_class'];
            $breedId = $breedMapping[$classIndex] ?? 1;
            $breed = QuailBreed::whereIn('id', [1, 2, 3])->find($breedId);

            if (!$breed) {
                return $this->getRandomBreedResponse(
                    'Detected breed ID not found'
                );
            }

            return response()->json([
                'success' => true,
                'breed_id' => $breedId,
                'breed_name' => $breed->name,
                'confidence' => $result['confidence'] ?? 0.5,
                'fallback' => false
            ]);

        } catch (\Exception $e) {
            \Log::error(
                'Breed classification error: ' .
                $e->getMessage() .
                ' | ' .
                $e->getFile() .
                ':' .
                $e->getLine()
            );

            return $this->getRandomBreedResponse(
                'Error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Helper to return a random breed response
     */
    private function getRandomBreedResponse($reason = '')
    {
        // Only the 3 supported quail breeds
        $breeds = QuailBreed::whereIn('id', [1, 2, 3])->get();

        if ($breeds->isEmpty()) {
            return response()->json([
                'success' => true,
                'breed_id' => 1,
                'breed_name' => 'Japanese Quail',
                'confidence' => 0.5,
                'fallback' => true,
                'reason' => $reason
            ]);
        }

        $randomBreed = $breeds->random();

        return response()->json([
            'success' => true,
            'breed_id' => $randomBreed->id,
            'breed_name' => $randomBreed->name,
            'confidence' => 0.5,
            'fallback' => true,
            'reason' => $reason
        ]);
    }
}