
<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContactSupportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuailBreedController;
use App\Http\Controllers\QuailDetectionController;
use App\Http\Controllers\DetectionAnalyticsController;
// ...existing code...

Route::get('/', function () {
    return view('welcome2');
});

// Customer Routes
Route::get('/customer/register', [App\Http\Controllers\CustomerController::class, 'showRegistrationForm'])->name('customer.register');
Route::post('/customer/register', [App\Http\Controllers\CustomerController::class, 'register']);
Route::get('/customer/login', [App\Http\Controllers\CustomerController::class, 'showLoginForm'])->name('customer.login');
Route::post('/customer/login', [App\Http\Controllers\CustomerController::class, 'login']);
Route::post('/customer/logout', [App\Http\Controllers\CustomerController::class, 'logout'])->name('customer.logout');
Route::get('/customer/dashboard', [App\Http\Controllers\CustomerController::class, 'dashboard'])->name('customer.dashboard')->middleware('auth:customer');
Route::get('/customer/profile', [App\Http\Controllers\CustomerController::class, 'profile'])->name('customer.profile')->middleware('auth:customer');
Route::put('/customer/profile', [App\Http\Controllers\CustomerController::class, 'updateProfile'])->name('customer.profile.update')->middleware('auth:customer');
Route::get('/customer/orders', [App\Http\Controllers\CustomerController::class, 'orders'])->name('customer.orders')->middleware('auth:customer');
Route::get('/customer/orders/all', [App\Http\Controllers\CustomerController::class, 'allOrders'])->name('customer.orders.all')->middleware('auth:customer');
Route::get('/customer/orders/topay', [App\Http\Controllers\CustomerController::class, 'toPay'])->name('customer.orders.topay')->middleware('auth:customer');
Route::get('/customer/orders/toship', [App\Http\Controllers\CustomerController::class, 'toShip'])->name('customer.orders.toship')->middleware('auth:customer');
Route::get('/customer/orders/completed', [App\Http\Controllers\CustomerController::class, 'completed'])->name('customer.orders.completed')->middleware('auth:customer');
Route::get('/customer/orders/cancelled', [App\Http\Controllers\CustomerController::class, 'cancelled'])->name('customer.orders.cancelled')->middleware('auth:customer');

// Chat Routes
Route::get('/customer/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('customer.chat')->middleware('auth:customer');
Route::post('/customer/chat/send', [App\Http\Controllers\ChatController::class, 'send'])->name('customer.chat.send')->middleware('auth:customer');
Route::get('/customer/chat/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('customer.chat.messages')->middleware('auth:customer');
Route::post('/customer/chat/rating', [App\Http\Controllers\ChatController::class, 'submitRating'])->name('customer.chat.rating')->middleware('auth:customer');

// Admin Chat Routes
Route::get('/admin/chat', [App\Http\Controllers\AdminChatController::class, 'index'])->name('admin.chat')->middleware('auth');
Route::get('/admin/chat/customer/{id}', [App\Http\Controllers\AdminChatController::class, 'getCustomerChat'])->name('admin.chat.customer')->middleware('auth');
Route::post('/admin/chat/send', [App\Http\Controllers\AdminChatController::class, 'sendMessage'])->name('admin.chat.send')->middleware('auth');
Route::get('/admin/chat/unread', [App\Http\Controllers\AdminChatController::class, 'getUnreadCount'])->name('admin.chat.unread')->middleware('auth');

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Register Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Forgot Password Routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Contact Support Route
Route::get('/contact-support', [ContactSupportController::class, 'show'])->name('contact.support');
Route::post('/contact-support', [ContactSupportController::class, 'store'])->name('contact.store');

// Products Route
Route::get('/products', function () {
    return view('products');
})->name('products');

// Order Route
Route::get('/order', function () {
    return view('order');
})->name('order')->middleware('auth:customer');

Route::post('/order', [App\Http\Controllers\OrderController::class, 'store'])->name('order.store')->middleware('auth:customer');
Route::get('/order/receipt/{id}', [App\Http\Controllers\OrderController::class, 'receipt'])->name('order.receipt')->middleware('auth:customer');
Route::post('/order/{id}/cancel', [App\Http\Controllers\OrderController::class, 'cancelOrder'])->name('order.cancel')->middleware('auth:customer');
Route::post('/admin/order/{id}/status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('admin.order.updateStatus')->middleware('auth');

// Reviews routes
Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store')->middleware('auth:customer');
Route::get('/api/reviews/{slug}', [App\Http\Controllers\ReviewController::class, 'productReviews'])->name('reviews.product');
Route::get('/api/admin/reviews', [App\Http\Controllers\ReviewController::class, 'indexApi'])->name('admin.reviews.api');
Route::get('/admin/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('admin.reviews')->middleware('auth');
Route::post('/admin/reviews/{id}/reply', [App\Http\Controllers\ReviewController::class, 'reply'])->name('admin.reviews.reply')->middleware('auth');

// Customer notification routes
Route::get('/api/customer/notifications', [App\Http\Controllers\CustomerController::class, 'getNotifications'])->middleware('auth:customer');
Route::post('/api/customer/notifications/mark-read', [App\Http\Controllers\CustomerController::class, 'markNotificationsRead'])->middleware('auth:customer');
Route::get('/api/customer/order-status/{id}', [App\Http\Controllers\CustomerController::class, 'getOrderStatus'])->middleware('auth:customer');

// Orders & Chat API for admin dashboard
Route::get('/api/orders/pending', function() {
    $orders = \App\Models\Order::where('status', 'pending')->orderBy('created_at', 'desc')->get();
    $lastOrder = $orders->first();

    // Chat unread
    $unreadChats = \App\Models\Chat::where('sender', 'customer')->where('status', 'sent');
    $chatCount = $unreadChats->count();
    $lastChat = $unreadChats->orderBy('id', 'desc')->first();

    // Latest cancelled order (for cancellation notifications)
    $lastCancelled = \App\Models\Order::where('status', 'cancelled')
        ->orderByDesc('cancelled_at')
        ->orderByDesc('id')
        ->first();

    return response()->json([
        'lastId' => $lastOrder ? $lastOrder->id : 0,
        'count' => $orders->count(),
        'chatCount' => $chatCount,
        'lastChatId' => $lastChat ? $lastChat->id : 0,
        'lastCancellationId' => $lastCancelled ? $lastCancelled->id : 0,
        'lastCancelledOrder' => $lastCancelled ? [
            'id' => $lastCancelled->id,
            'customer' => $lastCancelled->name,
            'product' => $lastCancelled->product,
            'quantity' => $lastCancelled->quantity,
            'reason' => $lastCancelled->cancellation_reason,
            'time' => optional($lastCancelled->cancelled_at ?: $lastCancelled->created_at)->diffForHumans(),
        ] : null,
        'lastOrder' => $lastOrder ? [
            'id' => $lastOrder->id,
            'fullname' => $lastOrder->name,
            'product' => $lastOrder->product,
            'quantity' => $lastOrder->quantity,
            'contact' => $lastOrder->phone,
            'location' => $lastOrder->address,
            'created_at' => $lastOrder->created_at,
            'message' => "Name: {$lastOrder->name}\nContact: {$lastOrder->phone}\nLocation: {$lastOrder->address}\nProduct: {$lastOrder->product}\nQty: {$lastOrder->quantity}"
        ] : null
    ]);
})->middleware('auth');

Route::get('/api/orders/all', function() {
    $orders = \App\Models\Order::orderBy('created_at', 'desc')->get()->map(function($order) {
        return [
            'id' => $order->id,
            'fullname' => $order->name,
            'product' => $order->product,
            'quantity' => $order->quantity,
            'contact' => $order->phone,
            'location' => $order->address,
            'created_at' => $order->created_at,
            'status' => $order->status,
            'type' => $order->status === 'cancelled' ? 'cancelled_order' : 'order',
            'cancellation_reason' => $order->cancellation_reason,
            'message' => "Name: {$order->name}\nContact: {$order->phone}\nLocation: {$order->address}\nProduct: {$order->product}\nQty: {$order->quantity}\nOrder Type: pickup"
        ];
    });
    return response()->json($orders);
})->middleware('auth');

// Orders & Products page for admin
Route::get('/orders-products', function () {
    if (!auth()->check()) return redirect('/login');
    $customerOrders = \App\Models\Order::orderBy('created_at', 'desc')->get();
    return view('orders-products', compact('customerOrders'));
})->name('orders-products');

// Product routes
Route::get('/api/products', [ProductController::class, 'getProducts']);
Route::post('/products/{product}/price', [ProductController::class, 'updatePrice'])->middleware('auth');
Route::post('/products/{product}/stock', [ProductController::class, 'updateStock'])->middleware('auth');

// Sales routes
Route::post('/api/sales', [App\Http\Controllers\SalesController::class, 'store'])->middleware('auth');
Route::get('/api/sales', [App\Http\Controllers\SalesController::class, 'getSales'])->middleware('auth');
Route::get('/api/sales/analytics', [App\Http\Controllers\SalesController::class, 'getSalesAnalytics'])->middleware('auth');
Route::delete('/api/sales/{id}', [App\Http\Controllers\SalesController::class, 'destroy'])->middleware('auth');

// Egg Collection routes
Route::post('/api/egg-collections', [App\Http\Controllers\EggCollectionController::class, 'store'])->middleware('auth');
Route::get('/api/egg-collections', [App\Http\Controllers\EggCollectionController::class, 'getCollections'])->middleware('auth');
Route::get('/api/egg-collections/{id}', [App\Http\Controllers\EggCollectionController::class, 'show'])->middleware('auth');
Route::put('/api/egg-collections/{id}', [App\Http\Controllers\EggCollectionController::class, 'update'])->middleware('auth');
Route::delete('/api/egg-collections/{id}', [App\Http\Controllers\EggCollectionController::class, 'destroy'])->middleware('auth');
Route::get('/api/egg-collections/analytics', [App\Http\Controllers\EggCollectionController::class, 'getCollectionAnalytics'])->middleware('auth');

// Quail Breed API routes
Route::get('/api/breeds/current', [QuailBreedController::class, 'getCurrentBreed']);
Route::get('/api/breeds', [QuailBreedController::class, 'getAllBreeds']);
Route::post('/api/breeds/set-current', [QuailBreedController::class, 'setCurrentBreed'])->middleware('auth');

// Analytics routes
Route::get('/api/analytics', [App\Http\Controllers\AnalyticsController::class, 'getAnalytics'])->middleware('auth');

// Advertising page
Route::get('/advertising', function () {
    return view('advertising');
})->name('advertising');

Route::get('/test-feeder-files', function() {
    return view('test-feeder-files');
});

Route::get('/test-feeding', function() {
    return view('test-feeding');
});

Route::middleware(['web'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Live dashboard summary API (eggs, sales, orders, sensor, inventory, feed)
    Route::get('/api/dashboard/summary', [DashboardController::class, 'summary'])
        ->middleware('auth')->name('api.dashboard.summary');

    // DB-backed notification acknowledgments (cross-device consistent)
    Route::get('/api/admin/notification-acks', [\App\Http\Controllers\AdminNotificationAckController::class, 'index'])->middleware('auth');
    Route::post('/api/admin/notification-acks', [\App\Http\Controllers\AdminNotificationAckController::class, 'store'])->middleware('auth');
    Route::post('/api/admin/notification-acks/clear', [\App\Http\Controllers\AdminNotificationAckController::class, 'clearAll'])->middleware('auth');

    // Unified notification feed for the sidebar bell
    Route::get('/api/admin/notification-feed', [\App\Http\Controllers\NotificationFeedController::class, 'feed'])
        ->middleware('auth')->name('api.admin.notification-feed');
    
    Route::get('/speed-test', function () {
        if (!auth()->check()) return redirect('/login');
        return view('speed-test');
    })->name('speed.test');



    Route::get('/feeder', [App\Http\Controllers\FeedingScheduleController::class, 'index'])->name('feeder');
    Route::post('/feeder/feed', [App\Http\Controllers\FeedingScheduleController::class, 'manualFeed'])->name('feeder.feed');
    Route::post('/feeder/toggle', [App\Http\Controllers\FeedingScheduleController::class, 'toggleFeeder'])->name('feeder.toggle');
    Route::delete('/feeder/history/{feedHistory}', [App\Http\Controllers\FeedingScheduleController::class, 'deleteHistory'])->name('feeder.history.delete');

    // Game routes
    Route::get('/game', function () {
        if (!auth()->check()) return redirect('/login');
        return view('game');
    })->name('game');

    Route::post('/game/score', function (Request $request) {
        $data = $request->validate([
            'score' => 'required|integer|min:0',
            'meta' => 'nullable|array'
        ]);

        // store anonymous global leaderboard entry
        \DB::table('game_scores')->insert([
            'score' => $data['score'],
            'meta' => isset($data['meta']) ? json_encode($data['meta']) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    });
    // JSON leaderboard endpoint used by the game frontend
    Route::get('/api/game/leaderboard', function () {
        $rows = \DB::table('game_scores')->orderBy('score', 'desc')->limit(20)->get();
        return response()->json(['rows' => $rows]);
    })->name('api.game.leaderboard');

    Route::get('/leaderboard', function () {
        $rows = \DB::table('game_scores')->orderBy('score', 'desc')->limit(20)->get();
        return view('leaderboard', ['rows' => $rows]);
    })->name('leaderboard');

    Route::get('/feeder/schedule', [App\Http\Controllers\FeedingScheduleController::class, 'index'])->name('feeder.schedule');


    Route::get('/orders', function () {
        if (!auth()->check()) return redirect('/login');
        $currentQuailBreed = \App\Helpers\QuailBreedHelper::getCurrentBreed();
        
        // Get orders from both tables
        $contactOrders = \App\Models\ContactMessage::where('subject', 'LIKE', 'ORDER:%')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $customerOrders = \App\Models\Order::orderBy('created_at', 'desc')->get();
        
        return view('orders', compact('currentQuailBreed', 'contactOrders', 'customerOrders'));
    })->name('orders');

    Route::get('/inventory', function () {
        if (!auth()->check()) return redirect('/login');
        $currentQuailBreed = \App\Helpers\QuailBreedHelper::getCurrentBreed();
        $farm = [
            'live_quails' => \App\Models\FarmSetting::get('live_quails', 0),
            'dead_quails' => \App\Models\FarmSetting::get('dead_quails', 0),
            'dressed_quails_stock' => \App\Models\FarmSetting::get('dressed_quails_stock', 0),
            'quail_count_date' => \App\Models\FarmSetting::get('quail_count_date', date('Y-m-d')),
            'farm_name' => \App\Models\FarmSetting::get('farm_name', "Escalona's Farm") ?: "Escalona's Farm",
            'farm_address' => \App\Models\FarmSetting::get('farm_address', 'Pagkakaisa, Naujan, Or. Mindoro') ?: 'Pagkakaisa, Naujan, Or. Mindoro',
            'farm_phone' => \App\Models\FarmSetting::get('farm_phone', '+63 917 123 4567') ?: '+63 917 123 4567',
            'farm_email' => \App\Models\FarmSetting::get('farm_email', 'escalona.farm@gmail.com') ?: 'escalona.farm@gmail.com'
        ];
        
        $customerOrders = \App\Models\Order::orderBy('created_at', 'desc')->get();
        
        return view('inventory', compact('currentQuailBreed', 'farm', 'customerOrders'));
    })->name('inventory');

    // Quail Detection Routes
    Route::get('/quail-detection', [QuailDetectionController::class, 'index'])->name('quail-detection');
    Route::post('/quail-detection/save', [QuailDetectionController::class, 'saveDetection'])->name('quail-detection.save');
    Route::post('/quail-detection/classify', [QuailDetectionController::class, 'classifyBreed'])->name('quail-detection.classify');
    Route::get('/quail-detection/breed/{id}', [QuailDetectionController::class, 'getBreedInfo'])->name('quail-detection.breed-info');
    Route::get('/quail-detection/history', [QuailDetectionController::class, 'getHistory'])->name('quail-detection.history');
    Route::get('/quail-detection/stats', [QuailDetectionController::class, 'getStats'])->name('quail-detection.stats');

    // Detection Analytics Routes
    Route::get('/detection-analytics', [DetectionAnalyticsController::class, 'index'])->name('detection-analytics');
    Route::get('/detection-analytics/export-csv', [DetectionAnalyticsController::class, 'exportCsv'])->name('detection-analytics.export-csv');
    Route::get('/detection-analytics/export-pdf', [DetectionAnalyticsController::class, 'exportPdf'])->name('detection-analytics.export-pdf');
    Route::get('/detection-analytics/feed-export-csv', [DetectionAnalyticsController::class, 'exportFeedCsv'])->name('detection-analytics.feed-export-csv');

    // Debug route to test order status update
    Route::get('/debug/orders', function() {
        if (!auth()->check()) return response()->json(['error' => 'Unauthorized'], 401);
        
        $orders = \App\Models\ContactMessage::where('subject', 'LIKE', 'ORDER:%')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'subject', 'status', 'created_at']);
            
        return response()->json([
            'success' => true,
            'orders' => $orders,
            'route_exists' => \Route::has('orders.updateStatus'),
            'csrf_token' => csrf_token()
        ]);
    })->name('debug.orders');

    Route::post('/orders/{id}/status', function ($id, \Illuminate\Http\Request $request) {
        if (!auth()->check()) return response()->json(['error' => 'Unauthorized'], 401);
        
        try {
            // Validate the ID parameter
            if (!$id || !is_numeric($id)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Invalid order ID: ' . $id
                ], 400);
            }
            
            // Try to find the order
            $order = \App\Models\ContactMessage::find($id);
            if (!$order) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Order not found with ID: ' . $id
                ], 404);
            }
            
            $status = $request->input('status', 'pending');
            
            // Validate status
            if (!in_array($status, ['pending', 'done', 'cancelled'])) {
                return response()->json(['success' => false, 'message' => 'Invalid status'], 400);
            }
            
            $order->status = $status;
            $order->save();
            
            return response()->json([
                'success' => true, 
                'status' => $order->status,
                'message' => 'Order status updated successfully',
                'order_id' => $id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error updating order status: ' . $e->getMessage()
            ], 500);
        }
    })->name('orders.updateStatus');

    Route::post('/orders/{id}/delete', function ($id) {
        if (!auth()->check()) return response()->json(['error' => 'Unauthorized'], 401);
        try {
            $order = \App\Models\ContactMessage::findOrFail($id);
            $order->delete();
            return response()->json(['success' => true, 'message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting order: ' . $e->getMessage()]);
        }
    })->name('orders.delete');

    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/farm-info', [App\Http\Controllers\SettingsController::class, 'saveFarmInfo'])->name('settings.farm-info');
    Route::post('/api/quail-stock/update', [App\Http\Controllers\SettingsController::class, 'updateQuailStock'])->middleware('auth');
    Route::post('/settings/feeder-schedule', [App\Http\Controllers\SettingsController::class, 'saveFeederSchedule'])->name('settings.feeder-schedule');
    
    Route::post('/language', function (Request $request) {
        if (!auth()->check()) return redirect('/login');
        $locale = $request->input('locale', 'en');
        if (in_array($locale, ['en', 'tl'])) {
            session(['locale' => $locale]);
        }
        return back();
    })->name('language.switch');

    Route::get('/temperature-humidity', [App\Http\Controllers\TemperatureHumidityController::class, 'index'])->name('temperature.humidity')->middleware('auth');
    
    // Sensor readings delete route
    Route::delete('/sensor-readings/{id}', function($id) {
        if (!auth()->check()) return response()->json(['error' => 'Unauthorized'], 401);
        try {
            $reading = \App\Models\SensorReading::findOrFail($id);
            $reading->delete();
            return response()->json(['success' => true, 'message' => 'Reading deleted']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting reading']);
        }
    })->name('sensor.readings.delete');

    // Debug servo route
    Route::get('/debug/servo', function() {
        $port = env('SERVO_SERIAL_PORT', 'COM4');
        $python = env('PYTHON_PATH', 'python');
        $script = base_path('scripts/servo_control.py');
        
        $command = "\"$python\" \"$script\" trigger 3 $port";
        
        echo "Command: $command\n\n";
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        echo "Return Code: $returnCode\n";
        echo "Output:\n" . implode("\n", $output);
    });

    // ...existing code...

    // ...existing code...

    // Debug route for schedule testing
    Route::get('/schedule-test', function() {
        return view('schedule-test');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // ...existing code...
});
