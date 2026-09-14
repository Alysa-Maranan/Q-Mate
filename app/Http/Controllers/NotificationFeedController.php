<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ContactMessage;
use App\Models\FarmSetting;
use App\Models\FeedHistory;
use App\Models\FoodLevel;
use App\Models\FeedingSchedule;
use App\Models\ManualFeed;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\SensorReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class NotificationFeedController extends Controller
{
    /**
     * Optimal climate ranges for quails (Temperature & Humidity alerts).
     */
    private const TEMP_MIN = 18.0;
    private const TEMP_MAX = 32.0;
    private const HUMIDITY_MIN = 40.0;
    private const HUMIDITY_MAX = 70.0;

    /**
     * GET /api/admin/notification-feed
     *
     * Unified notification feed for the sidebar bell. Aggregates:
     *  - Orders & Products      : pending orders, reviews awaiting a reply
     *  - Customer Chat          : unread contact messages + live chat messages
     *  - Feeder                 : food level low / empty
     *  - Temperature & Humidity : latest sensor reading outside optimal range
     *  - Inventory & Sales      : low live-quail count / dressed stock / product stock
     *
     * Items carry a stable "key" so the client can mark them acknowledged via
     * /api/admin/notification-acks (same store the dashboard popups use).
     */
    public function feed(): JsonResponse
    {
        $items = collect();

        // ── Orders & Products: pending orders ──────────────────────────
        try {
            Order::where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->each(function ($order) use ($items) {
                    $items->push([
                        'key'      => 'order_' . $order->id,
                        'category' => 'order',
                        'icon'     => '📋',
                        'title'    => 'New Order',
                        'message'  => trim(($order->name ?: 'A customer') .
                            ' ordered ' . ($order->quantity ?? '1') .
                            ' × ' . ($order->product ?? 'product')),
                        'time'     => optional($order->created_at)->format('M j, Y g:i A'),
                        '_ts'      => optional($order->created_at)->timestamp ?? 0,
                        'url'      => '/orders-products',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [orders]: ' . $e->getMessage());
        }

        // ── Orders & Products: recently cancelled orders ───────────────
        try {
            Order::where('status', 'cancelled')
                ->orderByDesc('cancelled_at')
                ->orderByDesc('id')
                ->limit(10)
                ->get()
                ->each(function ($order) use ($items) {
                    $when = $order->cancelled_at ?: $order->created_at;
                    $items->push([
                        'key'      => 'cancel_' . $order->id,
                        'category' => 'order',
                        'icon'     => '❌',
                        'title'    => 'Order Cancelled',
                        'message'  => trim(($order->name ?: 'A customer') . ' cancelled ' .
                            ($order->quantity ?? '1') . ' × ' . ($order->product ?? 'product')) .
                            ($order->cancellation_reason ? ' — Reason: ' . $order->cancellation_reason : ''),
                        'time'     => optional($when)->format('M j, Y g:i A'),
                        '_ts'      => optional($when)->timestamp ?? 0,
                        'url'      => '/orders-products',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [cancellations]: ' . $e->getMessage());
        }

        // ── Orders & Products: reviews waiting for an admin reply ──────
        try {
            Review::with('customer')
                ->whereNull('admin_reply')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->each(function ($review) use ($items) {
                    $items->push([
                        'key'      => 'review_' . $review->id,
                        'category' => 'review',
                        'icon'     => '💭',
                        'title'    => 'Review Needs a Reply',
                        'message'  => (optional($review->customer)->name ?: 'A customer') . ' left a product review.',
                        'time'     => optional($review->created_at)->format('M j, Y g:i A'),
                        '_ts'      => optional($review->created_at)->timestamp ?? 0,
                        'url'      => '/admin/reviews',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [reviews]: ' . $e->getMessage());
        }

        // ── Customer Chat: unread contact-form messages ────────────────
        try {
            ContactMessage::where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->each(function ($msg) use ($items) {
                    $items->push([
                        'key'      => 'contact_' . $msg->id,
                        'category' => 'chat',
                        'icon'     => '✉️',
                        'title'    => 'Customer Message',
                        'message'  => trim(($msg->fullname ?: 'A customer') . ': ' . Str::limit((string) $msg->message, 70)),
                        'time'     => optional($msg->created_at)->format('M j, Y g:i A'),
                        '_ts'      => optional($msg->created_at)->timestamp ?? 0,
                        'url'      => '/admin/chat',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [contact messages]: ' . $e->getMessage());
        }

        // ── Customer Chat: unread live-chat messages ───────────────────
        try {
            Chat::where('sender', 'customer')
                ->where('status', 'sent')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->each(function ($chat) use ($items) {
                    $items->push([
                        'key'      => 'chatmsg_' . $chat->id,
                        'category' => 'chat',
                        'icon'     => '💬',
                        'title'    => 'New Chat Message',
                        'message'  => Str::limit((string) $chat->message, 80),
                        'time'     => optional($chat->created_at)->format('M j, Y g:i A'),
                        '_ts'      => optional($chat->created_at)->timestamp ?? 0,
                        'url'      => '/admin/chat',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [chats]: ' . $e->getMessage());
        }

        // ── Feeder: food level low / empty ─────────────────────────────
        try {
            $food = FoodLevel::getCurrent();
            if (in_array($food->status, ['low', 'empty'])) {
                $items->push([
                    'key'      => 'feeder_food_' . now()->format('Ymd'),
                    'category' => 'feeder',
                    'icon'     => $food->status === 'empty' ? '⚠️' : '📊',
                    'title'    => $food->status === 'empty' ? 'Feeder Empty' : 'Feeder Food Low',
                    'message'  => $food->getStatusMessage() . ' (Level: ' . $food->level . '%)',
                    'time'     => optional($food->last_updated)->format('M j, Y g:i A'),
                    '_ts'      => optional($food->last_updated)->timestamp ?? now()->timestamp,
                    'url'      => '/feeder',
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [food level]: ' . $e->getMessage());
        }

        // ── Temperature & Humidity: latest reading outside optimal range ─
        try {
            $reading = SensorReading::latestReading()->first();
            if ($reading && $reading->recorded_at && $reading->recorded_at->greaterThan(now()->subHour())) {
                $temp = (float) $reading->temperature;
                $hum  = (float) $reading->humidity;

                if ($temp > 0 && ($temp < self::TEMP_MIN || $temp > self::TEMP_MAX)) {
                    $items->push([
                        'key'      => 'temp_alert_' . $reading->recorded_at->format('Ymd_H'),
                        'category' => 'climate',
                        'icon'     => '🌡',
                        'title'    => 'Temperature Alert',
                        'message'  => "Temperature is {$temp}°C — optimal range is " .
                            self::TEMP_MIN . '–' . self::TEMP_MAX . '°C.',
                        'time'     => $reading->recorded_at->format('M j, Y g:i A'),
                        '_ts'      => $reading->recorded_at->timestamp,
                        'url'      => '/temperature-humidity',
                    ]);
                }

                if ($hum > 0 && ($hum < self::HUMIDITY_MIN || $hum > self::HUMIDITY_MAX)) {
                    $items->push([
                        'key'      => 'humidity_alert_' . $reading->recorded_at->format('Ymd_H'),
                        'category' => 'climate',
                        'icon'     => '💧',
                        'title'    => 'Humidity Alert',
                        'message'  => "Humidity is {$hum}% — optimal range is " .
                            self::HUMIDITY_MIN . '–' . self::HUMIDITY_MAX . '%.',
                        'time'     => $reading->recorded_at->format('M j, Y g:i A'),
                        '_ts'      => $reading->recorded_at->timestamp,
                        'url'      => '/temperature-humidity',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [sensor readings]: ' . $e->getMessage());
        }

        // ── Inventory & Sales: low stock ───────────────────────────────
        try {
            $liveQuails = (int) FarmSetting::get('live_quails', 0);
            if ($liveQuails <= 0) {
                $items->push([
                    'key'      => 'inv_live_out_' . now()->format('Ymd'),
                    'category' => 'inventory',
                    'icon'     => '⚠',
                    'title'    => 'Out of Live Quails',
                    'message'  => 'Live quail count has reached zero. Restock soon.',
                    'time'     => now()->format('M j, Y g:i A'),
                    '_ts'      => now()->timestamp,
                    'url'      => '/inventory',
                ]);
            } elseif ($liveQuails < 20) {
                $items->push([
                    'key'      => 'inv_live_low_' . now()->format('Ymd'),
                    'category' => 'inventory',
                    'icon'     => '📊',
                    'title'    => 'Low Live Quail Stock',
                    'message'  => "Only {$liveQuails} live quails left in inventory.",
                    'time'     => now()->format('M j, Y g:i A'),
                    '_ts'      => now()->timestamp,
                    'url'      => '/inventory',
                ]);
            }

            $dressedStock = (int) FarmSetting::get('dressed_quails_stock', 0);
            if ($dressedStock < 10) {
                $items->push([
                    'key'      => 'inv_dressed_low_' . now()->format('Ymd'),
                    'category' => 'inventory',
                    'icon'     => '📦',
                    'title'    => 'Low Dressed Quail Stock',
                    'message'  => "Dressed quail stock is down to {$dressedStock}.",
                    'time'     => now()->format('M j, Y g:i A'),
                    '_ts'      => now()->timestamp,
                    'url'      => '/inventory',
                ]);
            }

            Product::where('is_active', true)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->limit(5)
                ->get()
                ->each(function ($product) use ($items) {
                    $items->push([
                        'key'      => 'inv_product_' . $product->id . '_' . now()->format('Ymd'),
                        'category' => 'inventory',
                        'icon'     => '📦',
                        'title'    => 'Low Product Stock',
                        'message'  => "{$product->name} stock is down to {$product->stock}.",
                        'time'     => now()->format('M j, Y g:i A'),
                        '_ts'      => now()->timestamp,
                        'url'      => '/inventory',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [inventory]: ' . $e->getMessage());
        }

        // ── Feeder: feeding in progress (FEEDING NOW) ───────────────────
        // State-driven: appears while a feeding is running, disappears when
        // the background done-command flips the status. Only recent statuses
        // are shown (stale ones from DB outages are ignored).
        try {
            FeedingSchedule::where('status', 'feeding_now')
                ->where('feeding_started_at', '>=', now()->subMinutes(31))
                ->get()
                ->each(function ($s) use ($items) {
                    $ts = $s->feeding_started_at ? strtotime((string) $s->feeding_started_at) : time();
                    $items->push([
                        'key'      => 'feed_now_sched_' . $s->id . '_' . $ts,
                        'category' => 'feeder',
                        'icon'     => '🍲',
                        'title'    => 'Feeding Now',
                        'message'  => 'Scheduled feeding in progress for Cage ' . ($s->cage_number ?? 1) .
                                      ' — servo open (' . ($s->amount ?? 5) . ' sec).',
                        'time'     => date('M j, Y g:i A', $ts),
                        '_ts'      => $ts,
                        'url'      => '/feeder',
                    ]);
                });

            ManualFeed::where('status', 'feeding_now')
                ->where('created_at', '>=', now()->subMinutes(30))
                ->orderByDesc('id')
                ->limit(5)
                ->get()
                ->each(function ($m) use ($items) {
                    $raw = $m->started_at ?: $m->created_at;
                    $ts  = $raw ? strtotime((string) $raw) : time();
                    $items->push([
                        'key'      => 'feed_now_manual_' . $m->id . '_' . $ts,
                        'category' => 'feeder',
                        'icon'     => '🍲',
                        'title'    => 'Feeding Now',
                        'message'  => 'Manual feeding in progress for Cage ' . ($m->cage_number ?? 1) .
                                      ' — servo open (' . ($m->duration ?? 5) . ' sec).',
                        'time'     => date('M j, Y g:i A', $ts),
                        '_ts'      => $ts,
                        'url'      => '/feeder',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [feeding now]: ' . $e->getMessage());
        }

        // ── Feeder: feedings completed in the last 10 minutes (DONE FEEDING) ──
        // Records are created by the feeder:manual-done / feeder:schedule-done
        // background commands right after the servo closes.
        try {
            FeedHistory::where('status', 'completed')
                ->where('fed_at', '>=', now()->subMinutes(10))
                ->orderByDesc('fed_at')
                ->limit(5)
                ->get()
                ->each(function ($h) use ($items) {
                    $ts   = $h->fed_at ? strtotime((string) $h->fed_at) : time();
                    $type = ($h->feed_type === 'manual') ? 'Manual' : 'Scheduled';
                    $items->push([
                        'key'      => 'feed_done_' . $h->id,
                        'category' => 'feeder',
                        'icon'     => '✅',
                        'title'    => 'Done Feeding',
                        'message'  => $type . ' feeding completed for Cage ' . ($h->cage_number ?? 1) .
                                      ' (' . ($h->duration ?? 5) . ' sec).',
                        'time'     => date('M j, Y g:i A', $ts),
                        '_ts'      => $ts,
                        'url'      => '/feeder',
                    ]);
                });
        } catch (\Throwable $e) {
            \Log::warning('Notification feed [done feeding]: ' . $e->getMessage());
        }

        // Sort newest first, cap the list, expose a client-friendly "ts" unix timestamp
        $items = $items
            ->sortByDesc('_ts')
            ->values()
            ->take(40)
            ->map(fn ($item) => collect($item)
                ->put('ts', $item['_ts'] ?? 0)
                ->except('_ts')
                ->all())
            ->all();

        return response()->json([
            'success' => true,
            'count'   => count($items),
            'items'   => $items,
        ]);
    }
}
