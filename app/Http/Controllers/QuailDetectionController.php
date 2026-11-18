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
            'detection_type' => 'required|in:quail,human,object,unknown',
            'quail_breed_id' => 'nullable|exists:quail_breeds,id',
            'confidence' => 'nullable|numeric|min:0|max:1',
            'location' => 'nullable|string|max:255',
            'detection_notes' => 'nullable|string',
            'image_data' => 'nullable|string',

            // Full normalized detection result shown in the modal.
            'client_detection_id' => 'nullable|string|max:100',
            'detected_at' => 'nullable|date',
            'object_class' => 'nullable|string|max:100',
            'object_detection_result' => 'nullable|string|max:100',
            'object_confidence' => 'nullable|numeric|min:0|max:1',
            'classification_status' => 'nullable|string|max:50',
            'breed_name' => 'nullable|string|max:255',
            'model_class_name' => 'nullable|string|max:100',
            'breed_reference_key' => 'nullable|string|max:100',
            'breed_confidence' => 'nullable|numeric|min:0|max:1',
            'breed_reference' => 'nullable|array',
            'temperature' => 'nullable|numeric|min:-40|max:80',
            'humidity' => 'nullable|numeric|min:0|max:100',
            'sensor_source' => 'nullable|string|max:50',
            'sensor_status' => 'nullable|string|max:50',
            'recorded_at' => 'nullable|date',
        ]);

        try {
            $imagePath = null;

            if (!empty($request->input('image_data'))) {
                $imageData = (string) $request->input('image_data');

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

            /*
             * Keep the complete modal payload in the existing JSON column so
             * history can reproduce the exact recorded values without a
             * new prediction or a breed default. The legacy confidence
             * column keeps storing the object-detection score for reports.
             */

            $resultData = [
                'detection_id' =>
                    $validated['client_detection_id'] ?? null,
                'detected_at' =>
                    $validated['detected_at'] ?? null,
                'detection_type' =>
                    $validated['detection_type'],
                'object_class' =>
                    $validated['object_class']
                    ?? ($validated['object_detection_result'] ?? null),
                'object_detection_result' =>
                    $validated['object_detection_result'] ?? null,
                'object_confidence' =>
                    $validated['object_confidence']
                    ?? ($validated['confidence'] ?? null),
                'classification_status' =>
                    $validated['classification_status'] ?? 'not_applicable',
                'breed_name' =>
                    $validated['breed_name'] ?? null,
                'model_class_name' =>
                    $validated['model_class_name'] ?? null,
                'breed_reference_key' =>
                    $validated['breed_reference_key'] ?? null,
                'breed_confidence' =>
                    $validated['breed_confidence'] ?? null,
                'breed_reference' =>
                    $validated['breed_reference'] ?? null,
                'temperature' =>
                    $validated['temperature'] ?? null,
                'humidity' =>
                    $validated['humidity'] ?? null,
                'sensor_source' =>
                    $validated['sensor_source'] ?? null,
                'sensor_status' =>
                    $validated['sensor_status'] ?? 'unavailable',
                'sensor_recorded_at' =>
                    $validated['recorded_at'] ?? null,
                'image_path' => $imagePath,
            ];

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
                'raw_detection_data' => $resultData,
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
             * PYTHON ENVIRONMENT
             * ==========================================================
             *
             * Prefer an explicitly configured interpreter, then the
             * Docker venv, then the project-local venv used on Windows.
             */

            $pythonCandidates = array_values(array_filter([
                env('PYTHON_PATH'),
                '/opt/qmate-venv/bin/python',
                base_path('.venv/Scripts/python.exe'),
                base_path('.venv/bin/python'),
            ]));

            $pythonExecutable = $pythonCandidates[0]
                ?? '/opt/qmate-venv/bin/python';

            foreach ($pythonCandidates as $candidate) {
                if (is_file($candidate) && is_executable($candidate)) {
                    $pythonExecutable = $candidate;
                    break;
                }
            }

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
                '%s %s %s %s %s 2>&1',
                escapeshellarg($pythonExecutable),
                escapeshellarg($pythonScript),
                escapeshellarg($fullPath),
                escapeshellarg($modelPath),
                escapeshellarg($metadataPath)
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
             * 10. MAP MODEL CLASS TO THE SAVED LABEL AND DATABASE BREED
             * ==========================================================
             *
             * The label order is read from model_metadata.json; the model
             * class index is never assumed to imply a particular breed.
             */

            $metadata = [];

            if (is_file($metadataPath)) {
                $decodedMetadata = json_decode(
                    (string) file_get_contents($metadataPath),
                    true
                );

                if (is_array($decodedMetadata)) {
                    $metadata = $decodedMetadata;
                }
            }

            $metadataClassNames = [];

            if (
                isset($metadata['class_names']) &&
                is_array($metadata['class_names'])
            ) {
                foreach ($metadata['class_names'] as $index => $label) {
                    $metadataClassNames[(int) $index] = (string) $label;
                }
            }

            $metadataDisplayNames = [];

            if (
                isset($metadata['display_names']) &&
                is_array($metadata['display_names'])
            ) {
                foreach ($metadata['display_names'] as $label => $displayName) {
                    $metadataDisplayNames[(string) $label] = (string) $displayName;
                }
            }

            $classLabel = $metadataClassNames[$classIndex]
                ?? (isset($result['class_name'])
                    ? trim((string) $result['class_name'])
                    : '');

            if ($classLabel === '') {
                throw new \RuntimeException(
                    'No saved class label for predicted class: ' .
                    $classIndex
                );
            }

            /*
             * Map the saved label (not the class index) to the configured
             * reference data. Legacy labels are accepted only when they
             * resolve to one of the two supported trained classes.
             */

            $referenceKeyByLabel = [
                'japanese_quail_group' => 'japanese_quail_group',
                'japanese_quail' => 'japanese_quail_group',
                'japanese_coturnix_crossbreed_taiwan' => 'japanese_quail_group',
                'pharaoh_quail' => 'pharaoh_quail',
            ];

            $referenceKey =
                $referenceKeyByLabel[$classLabel] ?? null;

            if ($referenceKey === null) {
                throw new \RuntimeException(
                    'No breed reference mapping for model class label: ' .
                    $classLabel
                );
            }

            $breedReference = config(
                'quail.breed_references.' . $referenceKey
            );

            if (!is_array($breedReference)) {
                throw new \RuntimeException(
                    'Breed reference configuration is missing for: ' .
                    $referenceKey
                );
            }

            $breedId = (int) $breedReference['breed_id'];

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
             * The display name and reference values come from the saved
             * configuration for this trained class. The database breed is
             * still required so existing breed relations keep working.
             */

            $breedName = (string) $breedReference['common_name'];

            if ($breedName === '') {
                $breedName = (string) (
                    $metadataDisplayNames[$classLabel]
                    ?? ($result['breed_name'] ?? $breed->name)
                );
            }

            $normalizedReference = [
                'reference_key' => $referenceKey,
                'breed_id' => $breedId,
                'common_name' => $breedName,
                'scientific_name' => (string) $breedReference['scientific_name'],
                'species' => (string) $breedReference['species'],
                'breed_or_variety' => (string) $breedReference['breed_or_variety'],
                'typical_adult_weight' => (string) $breedReference['typical_adult_weight'],
                'typical_maturity_age' => (string) $breedReference['typical_maturity_age'],
                'typical_egg_production' => (string) $breedReference['typical_egg_production'],
                'optimal_temperature' => (string) ($breedReference['optimal_temperature'] ?? ''),
                'optimal_humidity' => (string) ($breedReference['optimal_humidity'] ?? ''),
                'description' => (string) ($breedReference['description'] ?? ''),
                'reference_note' => (string) $breedReference['reference_note'],
            ];

            /*
             * ==========================================================
             * 12. GET ACTUAL MODEL INFORMATION
             * ==========================================================
             */

            $modelConfidence =
                isset($result['confidence'])
                    ? (float) $result['confidence']
                    : 0.0;

            $modelClassName = $classLabel;

            $confidenceThreshold =
                (float) config('quail.confidence_threshold', 0.60);

            /*
             * Never force a breed identification when the model is not
             * confident enough. The frontend shows "Uncertain Detection".
             */

            $uncertain = $modelConfidence < $confidenceThreshold;

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
                        $breedName,

                    'model_confidence' =>
                        $modelConfidence,

                    'uncertain' =>
                        $uncertain,

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

                'uncertain' =>
                    $uncertain,

                'confidence_threshold' =>
                    $confidenceThreshold,

                'message' =>
                    $uncertain
                        ? 'Uncertain Detection'
                        : 'Breed classified successfully.',

                'breed_id' =>
                    $uncertain ? null : $breedId,

                'breed_name' =>
                    $uncertain ? null : $breedName,

                'breed_reference_key' =>
                    $uncertain ? null : $referenceKey,

                'breed_reference' =>
                    $uncertain ? null : $normalizedReference,

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