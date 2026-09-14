<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\FeederCommandController;
use App\Http\Controllers\FoodLevelController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\EggCollectionController;
use App\Http\Controllers\QuailBreedController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ── Analytics API Routes ──
Route::get('/analytics', [AnalyticsController::class, 'getAnalytics']);

// ── Quail Breed API Routes ──
Route::get('/breeds/current', [QuailBreedController::class, 'getCurrentBreed']);
Route::get('/breeds', [QuailBreedController::class, 'getAllBreeds']);
Route::post('/breeds/set-current', [QuailBreedController::class, 'setCurrentBreed'])->middleware('auth');
Route::post('/breeds/set-from-detection', [QuailBreedController::class, 'setFromDetection']);

// ── Egg Collection API Routes ──
Route::get('/egg-collections', [EggCollectionController::class, 'index']);
Route::post('/egg-collections', [EggCollectionController::class, 'store']);
Route::get('/egg-collections/{id}', [EggCollectionController::class, 'show']);
Route::put('/egg-collections/{id}', [EggCollectionController::class, 'update']);
Route::delete('/egg-collections/{id}', [EggCollectionController::class, 'destroy']);

// ── Products API Routes ──
Route::get('/products', function (Request $request) {
    $products = [
        ['id' => 1, 'name' => 'Fresh Quail Eggs', 'unit' => 'tray (24 pieces)', 'price' => 80, 'stock' => 50, 'slug' => 'fresh-quail-eggs', 'image_url' => 'https://www.heritageacresmarket.com/wp-content/uploads/2021/04/quail-eggs-1536x1024.jpg'],
        ['id' => 2, 'name' => 'Live Quail', 'unit' => 'piece', 'price' => 180, 'stock' => 25, 'slug' => 'live-quail', 'image_url' => 'https://tse2.mm.bing.net/th/id/OIP.Ge3tGykJY_QCdnTN79DgMwHaE8?rs=1&pid=ImgDetMain&o=7&rm=3'],
        ['id' => 3, 'name' => 'Dressed Quail', 'unit' => 'piece (cleaned)', 'price' => 250, 'stock' => 15, 'slug' => 'dressed-quail', 'image_url' => 'https://tse3.mm.bing.net/th/id/OIP.f-7v4Sb2MI3CLuxVd5MfMQHaHa?rs=1&pid=ImgDetMain&o=7&rm=3']
    ];

    if ($request->has('search') && $request->search != '') {
        $search = strtolower($request->search);
        $products = array_filter($products, function($product) use ($search) {
            return str_contains(strtolower($product['name']), $search);
        });
        $products = array_values($products);
    }

    return response()->json($products);
});

// ── Food Level API Routes (para sa ESP32 HC-SR04 ultrasonic sensor) ──
// ESP32 sends food level percentage dito every 10 seconds
Route::post('/food-level', [FoodLevelController::class, 'updateFromSensor']);
// Dashboard polls dito para ma-update ang food level display in real-time
Route::get('/food-level/current', [FoodLevelController::class, 'getCurrent']);
// Check kung pwedeng mag-feed (hindi empty ang pagkain)
Route::get('/food-level/can-feed', [FoodLevelController::class, 'canFeed']);

// ── Feeder Command API Routes (para sa ESP32 servo motor) ──
// ESP32 polls dito every 2 seconds para sa pending feed commands
Route::get('/feeder/pending-command', [FeederCommandController::class, 'getPendingCommand']);
// ESP32 calls dito kapag tapos na ang feeding
Route::post('/feeder/command-done', [FeederCommandController::class, 'commandDone']);

// ── Sensor Data API Routes (para sa WEMOS DHT22) ──
// Store sensor readings
Route::post('/sensor-data', [SensorDataController::class, 'store']);
// Manual entry for testing
Route::post('/sensor-data/manual-entry', [SensorDataController::class, 'store']);
// Get latest reading
Route::get('/sensor-data/latest', [SensorDataController::class, 'getLatest']);
// Get historical data
Route::get('/sensor-data/history', [SensorDataController::class, 'getHistory']);
// Get average readings
Route::get('/sensor-data/average', [SensorDataController::class, 'getAverage']);
// Get Philippine weather data
Route::get('/weather/philippines', [\App\Http\Controllers\TemperatureHumidityController::class, 'getPhilippineWeather']);

// ── Dashboard Summary API (real-time polling) ──
Route::get('/dashboard/summary', function () {
    // Sensor data
    $latestSensor = \App\Models\SensorReading::latest('recorded_at')->first();
    $sensor = null;
    if ($latestSensor) {
        $sensor = [
            'temperature' => (float) $latestSensor->temperature,
            'humidity' => (float) $latestSensor->humidity,
            'recorded_at' => $latestSensor->recorded_at ? $latestSensor->recorded_at->
format('g:i A') : null,
        ];
    }

    return response()->json([
        'success' => true,
        'sensor' => $sensor,
    ]);
});

// ── Orders API Routes (for order notifications) ──
// Returns pending orders count + latest order/cancellation for dashboard popups.
// Orders live in the `orders` table (OrderController@store) — the old
// ContactMessage "ORDER:%" flow is deprecated and no longer written to.
Route::get('/orders/pending', function () {
    $pendingCount = \App\Models\Order::where('status', 'pending')->count();

    $lastOrder = \App\Models\Order::orderBy('id', 'desc')->first();
    $lastOrderData = null;
    if ($lastOrder) {
        $lastOrderData = [
            'id'         => $lastOrder->id,
            'status'     => $lastOrder->status,
            'customer'   => $lastOrder->name,
            'product'    => $lastOrder->product,
            'quantity'   => $lastOrder->quantity,
            'contact'    => $lastOrder->phone,
            'time'       => optional($lastOrder->created_at)->diffForHumans(),
            'created_at' => optional($lastOrder->created_at)->toIso8601String(),
            'address'    => $lastOrder->address,
        ];
    }

    $lastCancelled = \App\Models\Order::where('status', 'cancelled')
        ->orderByDesc('cancelled_at')
        ->orderByDesc('id')
        ->first();
    $lastCancelledData = null;
    if ($lastCancelled) {
        $lastCancelledData = [
            'id'         => $lastCancelled->id,
            'status'     => 'cancelled',
            'customer'   => $lastCancelled->name,
            'product'    => $lastCancelled->product,
            'quantity'   => $lastCancelled->quantity,
            'reason'     => $lastCancelled->cancellation_reason,
            'time'       => optional($lastCancelled->cancelled_at ?: $lastCancelled->created_at)->diffForHumans(),
            'created_at' => optional($lastCancelled->cancelled_at ?: $lastCancelled->created_at)->toIso8601String(),
        ];
    }

    return response()->json([
        'count'              => $pendingCount,
        'lastId'             => $lastOrder ? $lastOrder->id : 0,
        'lastOrder'          => $lastOrderData,
        'lastCancellationId' => $lastCancelled ? $lastCancelled->id : 0,
        'lastCancelledOrder' => $lastCancelledData,
    ]);
});

// Returns recent order updates (new orders + cancellations) for notification bell
Route::get('/orders/recent', function () {
    $lastCheck = request()->has('since') ? \Carbon\Carbon::parse(request()->since) : \Carbon\Carbon::now()->subMinutes(30);

    // New orders since last check (orders are stored in the `orders` table
    // by OrderController@store — the old ContactMessage flow is deprecated)
    $newOrders = \App\Models\Order::where('created_at', '>=', $lastCheck)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($order) {
            $product = $order->product ? str_replace('_', ' ', $order->product) : 'product';

            return [
                'id' => $order->id,
                'type' => 'new_order',
                'message' => 'New Order: ' . ($order->name ?: 'A customer') . ' ordered ' . ($order->quantity ?? '1') . ' × ' . $product,
                'details' => $order->notes ? Str::limit($order->notes, 50) : ($order->address ? 'Deliver to: ' . Str::limit($order->address, 40) : ''),
                'time' => $order->created_at->diffForHumans(),
                'timestamp' => $order->created_at->toIso8601String()
            ];
        });

    // Cancelled orders since last check
    $cancelledOrders = \App\Models\Order::where('status', 'cancelled')
        ->where('cancelled_at', '>=', $lastCheck)
        ->orderBy('cancelled_at', 'desc')
        ->get()
        ->map(function($order) {
            return [
                'id' => $order->id,
                'type' => 'cancelled',
                'message' => 'Order Cancelled: ' . $order->product,
                'details' => $order->name . ' - ' . $order->cancellation_reason,
                'time' => $order->cancelled_at->diffForHumans(),
                'timestamp' => $order->cancelled_at->toIso8601String()
            ];
        });

    // Combine and sort by timestamp
    $allUpdates = $newOrders->merge($cancelledOrders)->sortByDesc('timestamp')->take(10)->values();

    return response()->json([
        'updates' => $allUpdates,
        'newCount' => $newOrders->count(),
        'cancelledCount' => $cancelledOrders->count()
    ]);
});

// Returns all orders for display in dashboard notifications
Route::get('/orders/all', function () {
    $orders = \App\Models\Order::orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function ($order) {
            return [
                'id'                  => $order->id,
                'type'                => $order->status === 'cancelled' ? 'cancelled_order' : 'new_order',
                'fullname'            => $order->name,
                'message'             => $order->notes,
                'subject'             => 'ORDER:' . $order->status . ' - ' . $order->product,
                'created_at'          => optional($order->created_at)->toIso8601String(),
                'product'             => $order->product,
                'quantity'            => $order->quantity,
                'contact'             => $order->phone,
                'location'            => $order->address,
                'status'              => $order->status,
                'cancellation_reason' => $order->cancellation_reason,
                'is_read'             => false,
            ];
        })
        ->values();

    return response()->json($orders);
});

// ── Chat/Contact Messages API Routes ──
// Get recent chat messages for notifications
Route::get('/chats/recent', function () {
    $chats = \App\Models\ContactMessage::where('subject', 'NOT LIKE', 'ORDER:%')
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function($msg) {
            return [
                'id' => $msg->id,
                'type' => 'chat',
                'fullname' => $msg->fullname,
                'email' => $msg->email,
                'location' => $msg->location,
                'subject' => $msg->subject,
                'message' => $msg->message,
                'created_at' => $msg->created_at->toIso8601String(),
                'time' => $msg->created_at->diffForHumans(),
                'is_read' => $msg->is_read
            ];
        });

    return response()->json($chats);
});

// Get pending/unread chat count (ContactMessage-based)
Route::get('/chats/pending', function () {
    $count = \App\Models\ContactMessage::where('subject', 'NOT LIKE', 'ORDER:%')
        ->where('is_read', false)
        ->count();

    $lastChat = \App\Models\ContactMessage::where('subject', 'NOT LIKE', 'ORDER:%')
        ->orderBy('id', 'desc')
        ->first();

    return response()->json([
        'count' => $count,
        'lastId' => $lastChat ? $lastChat->id : 0,
        'lastChat' => $lastChat
    ]);
});

// ── Chat with Us API Routes (from Chat model - customer conversations) ──
Route::get('/chat/messages/recent', function () {
    // Get recent customer messages sent to admin
    // Only get customer-sent messages, not admin auto-replies
    $chats = \App\Models\Chat::where('sender', 'customer')
        ->with('customer')
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function($chat) {
            return [
                'id' => $chat->id,
                'type' => 'chat_message',
                'chat_id' => $chat->id,
                'fullname' => $chat->customer->name ?? 'Customer',
                'email' => $chat->customer->email ?? 'N/A',
                'message' => $chat->message,
                'created_at' => $chat->created_at->toIso8601String(),
                'time' => $chat->created_at->diffForHumans(),
                'is_read' => $chat->status === 'read'
            ];
        });

    return response()->json($chats);
});

Route::get('/chat/messages/pending', function () {
    // Get unread customer messages count
    $count = \App\Models\Chat::where('sender', 'customer')
        ->where('status', 'sent')
        ->count();

    $lastChat = \App\Models\Chat::where('sender', 'customer')
        ->with('customer')
        ->orderBy('id', 'desc')
        ->first();

    return response()->json([
        'count' => $count,
        'lastId' => $lastChat ? $lastChat->id : 0,
        'lastChat' => $lastChat ? [
            'id' => $lastChat->id,
            'fullname' => $lastChat->customer->name ?? 'Customer',
            'email' => $lastChat->customer->email ?? 'N/A',
            'message' => $lastChat->message,
            'created_at' => $lastChat->created_at->toIso8601String(),
            'time' => $lastChat->created_at->diffForHumans()
        ] : null
    ]);
});

// ── Sensor Alerts (para sa notification bell) ──
Route::get('/sensor-alerts', function () {
    $file = storage_path('logs/sensor_alerts.json');
    if (!file_exists($file)) return response()->json([]);
    $alerts = json_decode(file_get_contents($file), true) ?? [];
    // Clear after reading so hindi paulit-ulit mag-notify
    file_put_contents($file, json_encode([]));
    return response()->json($alerts);
});

Route::get('/feeder/history', function () {
    if (session()->isStarted()) session()->save();
    $history = \App\Models\FeedHistory::select('id', 'feed_type', 'feed_brand', 'duration', 'status', 'fed_at', 'cage_number')
        ->latest('fed_at')
        ->limit(10)
        ->get()
        ->map(fn($h) => [
            'id'         => $h->id,
            'fed_at'     => \Carbon\Carbon::parse($h->fed_at)->format('M d, Y h:i A'),
            'cage'       => $h->cage_number ?? 1,
            'feed_brand' => $h->feed_brand ?? 'N/A',
            'duration'   => $h->duration,
            'status'     => $h->status,
        ]);
    return response()->json($history);
});

// ── Animation Trigger (para sa browser — sabay ng servo open/close) ──
Route::get('/feeder/animation', function () {
    $file = storage_path('logs/animation_trigger.json');
    if (!file_exists($file)) return response()->json(['active' => false]);
    $data = json_decode(file_get_contents($file), true);

    // STALE GUARD: kung lumipas na ang buong duration + 10s grace PERO hindi
    // pa na-delete ang file (namatay ang bridge sa gitna ng feeding),
    // itinuturing itong stale — burahin at i-report na walang active feeding.
    // Kaya HINDI lalabas ang "Dispensing Feed" animation kapag walang tunay
    // na feeding na nangyayari, kahit may naiwang file.
    if (!empty($data['started_at']) && !empty($data['duration'])) {
        $endsAt = strtotime($data['started_at']) + ((int) $data['duration'] + 10);
        if ($endsAt < time()) {
            @unlink($file);
            return response()->json(['active' => false]);
        }
    }

    return response()->json($data ?? ['active' => false]);
});

// ── Feeding Notification (para sa dashboard modal) ──
Route::get('/feeder/feeding-notification', function () {
    $file = storage_path('logs/feeding_notification.json');
    if (!file_exists($file)) return response()->json(['active' => false]);
    $data = json_decode(file_get_contents($file), true);
    // DO NOT delete file - user must click "OK, Got it" to confirm
    return response()->json($data ?? ['active' => false]);
});

// ── Confirm Feeding (deletes notification file to signal servo to start) ──
Route::post('/feeder/confirm-feeding', function () {
    $file = storage_path('logs/feeding_notification.json');
    if (file_exists($file)) {
        @unlink($file);
        return response()->json(['success' => true, 'message' => 'Feeding confirmed']);
    }
    return response()->json(['success' => false, 'message' => 'No notification to confirm']);
});

// ── Feeding Completion Notification (para sa modal after servo closes) ──
Route::get('/feeder/feeding-completed', function () {
    $file = storage_path('logs/feeding_completed.json');
    if (!file_exists($file)) return response()->json(['active' => false]);
    $data = json_decode(file_get_contents($file), true);
    // Delete file after reading so it only shows once
    if ($data && isset($data['active']) && $data['active']) {
        @unlink($file);
    }
    return response()->json($data ?? ['active' => false]);
});

// ── Customer Reviews API Route ──
Route::get('/my-reviews', function () {
    if (!auth('customer')->check()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $reviews = \App\Models\Review::where('customer_id', auth('customer')->id())
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($review) {
            return [
                'id' => $review->id,
                'product_name' => ucwords(str_replace('-', ' ', $review->product_slug)),
                'rating' => $review->rating,
                'comment' => $review->comment,
                'admin_reply' => $review->admin_reply,
                'formatted_date' => $review->created_at->format('M d, Y h:i A')
            ];
        });

    return response()->json(['success' => true, 'reviews' => $reviews]);
});

// ── Admin: All Reviews API Route ──
Route::get('/admin/reviews/all', function () {
    $reviews = \App\Models\Review::with('customer')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($review) {
            return [
                'id' => $review->id,
                'customer_name' => $review->customer->name ?? 'Unknown',
                'customer_email' => $review->customer->email ?? 'N/A',
                'product_slug' => $review->product_slug,
                'product_name' => ucwords(str_replace(['-', '_'], ' ', $review->product_slug)),
                'rating' => $review->rating,
                'comment' => $review->comment,
                'admin_reply' => $review->admin_reply,
                'created_at' => $review->created_at->toIso8601String(),
                'time' => $review->created_at->diffForHumans()
            ];
        });

    return response()->json(['success' => true, 'reviews' => $reviews]);
});

Route::get('/feeder/status', function () {
    // Release session lock immediately so this poll never blocks other requests
    if (session()->isStarted()) {
        session()->save();
    }

    try {
        // Clean up stale feeding_now (older than 5 min)
        \App\Models\FeedingSchedule::where('status', 'feeding_now')
            ->where('feeding_started_at', '<', now()->subMinutes(5))
            ->update(['status' => 'idle']);
        \App\Models\ManualFeed::where('status', 'feeding_now')
            ->where('created_at', '<', now()->subMinutes(3))
            ->update(['status' => 'done_feeding']);

        $active = \App\Models\FeedingSchedule::where('status', 'feeding_now')->first();
        $manual = \App\Models\ManualFeed::where('status', 'feeding_now')->latest()->first();
        $src    = $active ?? $manual;
        return response()->json([
            'feeding'            => (bool) $src,
            'duration'           => $src ? ($src->amount ?? $src->duration ?? 5) : null,
            'schedule_id'        => $active ? $active->id : null,
            'cage_number'        => $src ? ($src->cage_number ?? 1) : null,
            'feeding_started_at' => $src ? (isset($src->feeding_started_at) && $src->feeding_started_at ? $src->feeding_started_at->toIso8601String() : (isset($src->started_at) && $src->started_at ? $src->started_at->toIso8601String() : null)) : null,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'feeding' => false,
            'duration' => null,
            'schedule_id' => null,
            'error' => $e->getMessage()
        ], 200);
    }
});
