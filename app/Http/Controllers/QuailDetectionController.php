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
        if (!auth()->check()) {
            return redirect('/login');
        }

        $currentQuailBreed = QuailBreedHelper::getCurrentBreed();

        // Only the 3 supported quail breeds
        $quailBreeds = QuailBreed::whereIn('id', [1, 2, 3])->get();

        try {
            $recentDetections = DetectionHistory::where(
                'user_id',
                auth()->id()
            )
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $recentDetections = collect([]);
        }

        return view(
            'quail-detection',
            compact(
                'currentQuailBreed',
                'quailBreeds',
                'recentDetections'
            )
        );
    }

    /**
     * API endpoint to save detection results
     */
    public function saveDetection(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(
                ['error' => 'Unauthorized'],
                401
            );
        }

        $validated = $request->validate([
            'detection_type' => 'required|in:quail,human,unknown',
            'quail_breed_id' => 'nullable|exists:quail_breeds,id',
            'confidence' => 'nullable|numeric|min:0|max:1',
            'location' => 'nullable|string',
            'detection_notes' => 'nullable|string',
            'image_data' => 'nullable|string',
        ]);

        try {
            $imagePath = null;

            if ($request->has('image_data')) {
                $imageData = $request->input('image_data');

                if (strpos($imageData, 'base64') !== false) {
                    $image = str_replace(
                        'data:image/jpeg;base64,',
                        '',
                        $imageData
                    );

                    $image = str_replace(
                        'data:image/png;base64,',
                        '',
                        $image
                    );

                    $image = str_replace(
                        ' ',
                        '+',
                        $image
                    );

                    $filename =
                        'detection_' .
                        auth()->id() .
                        '_' .
                        time() .
                        '_' .
                        uniqid() .
                        '.jpg';

                    $path = storage_path(
                        'app/public/detections/' . $filename
                    );

                    if (!is_dir(dirname($path))) {
                        mkdir(
                            dirname($path),
                            0755,
                            true
                        );
                    }

                    file_put_contents(
                        $path,
                        base64_decode($image)
                    );

                    $imagePath =
                        'detections/' . $filename;
                }
            }

            $detection = DetectionHistory::create([
                'user_id' => auth()->id(),
                'quail_breed_id' =>
                    $validated['quail_breed_id'] ?? null,
                'detection_type' =>
                    $validated['detection_type'],
                'confidence' =>
                    $validated['confidence'] ?? null,
                'location' =>
                    $validated['location'] ?? null,
                'detection_notes' =>
                    $validated['detection_notes'] ?? null,
                'image_path' => $imagePath,
            ]);

            return response()->json([
                'success' => true,
                'message' =>
                    'Detection recorded successfully',
                'detection_id' => $detection->id,
            ]);
        } catch (\Exception $e) {
            \Log::error(
                'Quail detection error: ' .
                $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Error saving detection: ' .
                    $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get quail breed information with enhanced details
     */
    public function getBreedInfo($breedId)
    {
        if (!auth()->check()) {
            return response()->json(
                ['error' => 'Unauthorized'],
                401
            );
        }

        // Only allow the 3 supported breeds
        if (!in_array(
            (int) $breedId,
            [1, 2, 3],
            true
        )) {
            return response()->json(
                ['error' => 'Breed not found'],
                404
            );
        }

        $breed = QuailBreed::whereIn(
            'id',
            [1, 2, 3]
        )->find($breedId);

        if (!$breed) {
            return response()->json(
                ['error' => 'Breed not found'],
                404
            );
        }

        $compatibleFeeds = FeedItem::all()
            ->filter(function ($feed) use ($breedId) {
                return $feed->isSuitableForBreed(
                    $breedId
                ) !== false;
            })
            ->values();

        return response()->json([
            'success' => true,
            'breed' => [
                'id' => $breed->id,
                'name' => $breed->name,
                'scientific_name' =>
                    $breed->scientific_name,
                'description' =>
                    $breed->description,
                'color_markings' =>
                    $breed->color_markings,
                'size_category' =>
                    $breed->size_category,
                'egg_production_rate' =>
                    $breed->egg_production_rate,
                'mature_weight' =>
                    $breed->mature_weight,
                'maturity_age' =>
                    $breed->maturity_age,
                'optimal_temperature' =>
                    $breed->optimal_temperature,
                'optimal_humidity' =>
                    $breed->optimal_humidity,
                'care_requirements' =>
                    $breed->care_requirements,
                'recommended_feeds' =>
                    $breed->recommended_feeds,
                'common_diseases' =>
                    $breed->common_diseases,
                'image_url' =>
                    $breed->image_url,
            ],
            'compatible_feeds' =>
                $compatibleFeeds
                    ->map(fn($f) => [
                        'id' => $f->id,
                        'name' => $f->name,
                        'category' => $f->category,
                    ])
                    ->toArray(),
        ]);
    }

    /**
     * Get detection history
     */
    public function getHistory(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(
                ['error' => 'Unauthorized'],
                401
            );
        }

        $query = DetectionHistory::where(
            'user_id',
            auth()->id()
        )->with('breed');

        if ($request->has('type')) {
            $query->where(
                'detection_type',
                $request->input('type')
            );
        }

        if ($request->has('breed_id')) {
            $query->where(
                'quail_breed_id',
                $request->input('breed_id')
            );
        }

        $limit = $request->input(
            'limit',
            50
        );

        $detections = $query
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $detections,
        ]);
    }

    /**
     * Get detection statistics
     */
    public function getStats(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(
                ['error' => 'Unauthorized'],
                401
            );
        }

        $userId = auth()->id();

        $dateFrom = $request->input(
            'date_from',
            now()
                ->subDays(30)
                ->format('Y-m-d')
        );

        $dateTo = $request->input(
            'date_to',
            now()->format('Y-m-d')
        );

        $stats = [
            'total_detections' =>
                DetectionHistory::where(
                    'user_id',
                    $userId
                )
                    ->whereBetween(
                        'created_at',
                        [$dateFrom, $dateTo]
                    )
                    ->count(),

            'successful_detections' =>
                DetectionHistory::where(
                    'user_id',
                    $userId
                )
                    ->where(
                        'detection_type',
                        'quail'
                    )
                    ->whereBetween(
                        'created_at',
                        [$dateFrom, $dateTo]
                    )
                    ->count(),

            'human_detections' =>
                DetectionHistory::where(
                    'user_id',
                    $userId
                )
                    ->where(
                        'detection_type',
                        'human'
                    )
                    ->whereBetween(
                        'created_at',
                        [$dateFrom, $dateTo]
                    )
                    ->count(),

            'average_confidence' =>
                DetectionHistory::where(
                    'user_id',
                    $userId
                )
                    ->where(
                        'detection_type',
                        'quail'
                    )
                    ->whereBetween(
                        'created_at',
                        [$dateFrom, $dateTo]
                    )
                    ->avg('confidence'),

            'breeds_detected' =>
                DetectionHistory::where(
                    'user_id',
                    $userId
                )
                    ->where(
                        'detection_type',
                        'quail'
                    )
                    ->whereBetween(
                        'created_at',
                        [$dateFrom, $dateTo]
                    )
                    ->distinct(
                        'quail_breed_id'
                    )
                    ->count(),

            'by_breed' =>
                DetectionHistory::where(
                    'user_id',
                    $userId
                )
                    ->where(
                        'detection_type',
                        'quail'
                    )
                    ->whereBetween(
                        'created_at',
                        [$dateFrom, $dateTo]
                    )
                    ->selectRaw(
                        'quail_breed_id, COUNT(*) as count'
                    )
                    ->groupBy(
                        'quail_breed_id'
                    )
                    ->with('breed')
                    ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Classify quail breed from image
     * using the trained ML model.
     */
    public function classifyBreed(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'image' =>
                'required|image|max:5120',
        ]);

        $fullPath = null;

        try {
            /*
             * ==========================================================
             * 1. SAVE UPLOADED IMAGE TEMPORARILY
             * ==========================================================
             */

            $imagePath = $request
                ->file('image')
                ->store(
                    'temp/classifications',
                    'local'
                );

            $fullPath = storage_path(
                'app/' . $imagePath
            );

            /*
             * ==========================================================
             * 2. CLASSIFIER FILE PATHS
             * ==========================================================
             */

            $pythonScript = base_path(
                'classify_quail_breed.py'
            );

            $modelPath = base_path(
                'public/models/quail_breed_model.keras'
            );

            $metadataPath = base_path(
                'public/models/model_metadata.json'
            );

            /*
             * ==========================================================
             * RENDER / LINUX PYTHON
             * ==========================================================
             *
             * Dockerfile creates:
             *
             * /opt/qmate-venv/bin/python
             *
             * Do NOT use the Windows path:
             *
             * .venv\Scripts\python.exe
             *
             */

            $pythonExecutable = '/opt/qmate-venv/bin/python';

            /*
             * ==========================================================
             * 3. LOG PATHS
             * ==========================================================
             */

            \Log::info(
                'QUAIL BREED CLASSIFICATION PATHS',
                [
                    'python' =>
                        $pythonExecutable,

                    'python_exists' =>
                        file_exists(
                            $pythonExecutable
                        ),

                    'script' =>
                        $pythonScript,

                    'script_exists' =>
                        file_exists(
                            $pythonScript
                        ),

                    'model' =>
                        $modelPath,

                    'model_exists' =>
                        file_exists(
                            $modelPath
                        ),

                    'metadata' =>
                        $metadataPath,

                    'metadata_exists' =>
                        file_exists(
                            $metadataPath
                        ),

                    'image' =>
                        $fullPath,

                    'image_exists' =>
                        file_exists(
                            $fullPath
                        ),
                ]
            );

            /*
             * ==========================================================
             * 4. VERIFY REQUIRED FILES
             * ==========================================================
             */

            if (!file_exists(
                $pythonExecutable
            )) {
                throw new \RuntimeException(
                    'Python environment not found: ' .
                    $pythonExecutable
                );
            }

            if (!file_exists(
                $pythonScript
            )) {
                throw new \RuntimeException(
                    'Python classifier script not found: ' .
                    $pythonScript
                );
            }

            if (!file_exists(
                $modelPath
            )) {
                throw new \RuntimeException(
                    'Quail breed model not found: ' .
                    $modelPath
                );
            }

            if (!file_exists(
                $metadataPath
            )) {
                throw new \RuntimeException(
                    'Model metadata not found: ' .
                    $metadataPath
                );
            }

            if (!file_exists(
                $fullPath
            )) {
                throw new \RuntimeException(
                    'Uploaded image was not found: ' .
                    $fullPath
                );
            }

            /*
             * ==========================================================
             * 5. RUN THE ACTUAL TRAINED PYTHON CLASSIFIER
             * ==========================================================
             */

            $command = sprintf(
                '"%s" "%s" "%s" "%s" "%s" 2>&1',
                $pythonExecutable,
                $pythonScript,
                $fullPath,
                $modelPath,
                $metadataPath
            );

            \Log::info(
                'Executing trained quail breed classifier.'
            );

            $output = shell_exec(
                $command
            );

            \Log::info(
                'Python classifier output: ' .
                substr(
                    (string) $output,
                    0,
                    5000
                )
            );

            /*
             * ==========================================================
             * 6. CHECK PYTHON OUTPUT
             * ==========================================================
             */

            if (
                $output === null ||
                trim($output) === ''
            ) {
                throw new \RuntimeException(
                    'Python classifier returned no output.'
                );
            }

            /*
             * ==========================================================
             * 7. EXTRACT JSON FROM PYTHON OUTPUT
             * ==========================================================
             *
             * TensorFlow can output warnings/messages.
             * The classifier JSON is printed at the end.
             *
             * Search from bottom to top.
             */

            $lines = preg_split(
                '/\r?\n/',
                trim($output)
            );

            $result = null;

            for (
                $i = count($lines) - 1;
                $i >= 0;
                $i--
            ) {
                $line = trim(
                    $lines[$i]
                );

                if ($line === '') {
                    continue;
                }

                $decoded = json_decode(
                    $line,
                    true
                );

                if (
                    json_last_error() ===
                    JSON_ERROR_NONE &&
                    is_array($decoded)
                ) {
                    $result = $decoded;
                    break;
                }
            }

            /*
             * ==========================================================
             * 8. VALIDATE CLASSIFIER RESULT
             * ==========================================================
             */

            if (!is_array($result)) {
                throw new \RuntimeException(
                    'Unable to parse Python classifier JSON output.'
                );
            }

            if (
                !isset($result['success']) ||
                $result['success'] !== true
            ) {
                $pythonError =
                    isset($result['error'])
                        ? (string) $result['error']
                        : 'Python classifier failed.';

                throw new \RuntimeException(
                    $pythonError
                );
            }

            if (
                !isset(
                    $result['predicted_class']
                )
            ) {
                throw new \RuntimeException(
                    'Python classifier did not return predicted_class.'
                );
            }

            /*
             * ==========================================================
             * 9. GET PREDICTED CLASS
             * ==========================================================
             */

            $classIndex = (int)
                $result['predicted_class'];

            /*
             * ==========================================================
             * 10. MAP MODEL CLASS TO DATABASE BREED
             * ==========================================================
             *
             * MODEL:
             *
             * 0 = japanese_quail
             * 1 = japanese_coturnix_crossbreed_taiwan
             * 2 = pharaoh_quail
             *
             * DATABASE:
             *
             * 1 = Japanese Quail
             * 2 = Japanese Coturnix Crossbreed (Taiwan)
             * 3 = Pharaoh Quail
             */

            $classToBreedId = [
                0 => 1,
                1 => 2,
                2 => 3,
            ];

            /*
             * Do NOT silently convert invalid
             * classes to breed 1.
             */

            if (!array_key_exists(
                $classIndex,
                $classToBreedId
            )) {
                throw new \RuntimeException(
                    'Invalid predicted class returned by model: ' .
                    $classIndex
                );
            }

            $breedId =
                $classToBreedId[$classIndex];

            /*
             * ==========================================================
             * 11. GET BREED FROM DATABASE
             * ==========================================================
             */

            $breed = QuailBreed::find(
                $breedId
            );

            if (!$breed) {
                throw new \RuntimeException(
                    'Detected breed ID not found in database: ' .
                    $breedId
                );
            }

            /*
             * ==========================================================
             * 12. GET ACTUAL MODEL INFORMATION
             * ==========================================================
             */

            $modelConfidence =
                isset($result['confidence'])
                    ? (float) $result['confidence']
                    : 0.0;

            $modelClassName =
                isset($result['class_name'])
                    ? trim(
                        (string) $result['class_name']
                    )
                    : '';

            /*
             * If Python did not return a class name,
             * use the metadata class name based on the index.
             *
             * This is NOT a random breed.
             */

            if ($modelClassName === '') {
                $metadataClassNames = [
                    0 => 'japanese_quail',
                    1 =>
                        'japanese_coturnix_crossbreed_taiwan',
                    2 => 'pharaoh_quail',
                ];

                $modelClassName =
                    $metadataClassNames[
                        $classIndex
                    ] ?? '';
            }

            /*
             * ==========================================================
             * 13. LOG SUCCESS
             * ==========================================================
             */

            \Log::info(
                'QUAIL BREED CLASSIFICATION SUCCESS',
                [
                    'predicted_class' =>
                        $classIndex,

                    'model_class_name' =>
                        $modelClassName,

                    'breed_id' =>
                        $breedId,

                    'breed_name' =>
                        $breed->name,

                    'model_confidence' =>
                        $modelConfidence,

                    'all_predictions' =>
                        $result['all_predictions']
                        ?? [],
                ]
            );

            /*
             * ==========================================================
             * 14. RETURN ACTUAL CLASSIFIER RESULT
             * ==========================================================
             */

            return response()->json([
                'success' => true,

                'breed_id' =>
                    $breedId,

                'breed_name' =>
                    $breed->name,

                'model_class_name' =>
                    $modelClassName,

                'predicted_class' =>
                    $classIndex,

                'confidence' =>
                    $modelConfidence,

                'all_predictions' =>
                    $result['all_predictions']
                    ?? [],

                'fallback' => false,
            ]);

        } catch (\Throwable $e) {

            /*
             * ==========================================================
             * CLASSIFICATION ERROR
             * ==========================================================
             *
             * Never return a random breed.
             */

            \Log::error(
                'Breed classification error: ' .
                $e->getMessage() .
                ' | ' .
                $e->getFile() .
                ':' .
                $e->getLine()
            );

            return response()->json([
                'success' => false,

                'message' =>
                    'Breed classification failed.',

                'error' =>
                    $e->getMessage(),

                'fallback' => false,
            ], 500);

        } finally {

            /*
             * ==========================================================
             * DELETE TEMPORARY IMAGE
             * ==========================================================
             */

            if (
                $fullPath !== null &&
                file_exists($fullPath)
            ) {
                @unlink($fullPath);
            }
        }
    }
}