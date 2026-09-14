<?php

namespace App\Http\Controllers;

use App\Models\AdminNotificationAck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationAckController extends Controller
{
    /**
     * GET /api/admin/notification-acks
     * Returns all acknowledged notification keys for the current admin user.
     */
    public function index(Request $request)
    {
        $keys = AdminNotificationAck::query()
            ->where('user_id', $this->currentUserId())
            ->pluck('ack_key');

        return response()->json([
            'success' => true,
            'keys' => $keys,
        ]);
    }

    /**
     * POST /api/admin/notification-acks
     * Body: { "keys": ["order_12", "review_5", ...] }
     * Stores acknowledged keys (duplicates are ignored).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'keys' => 'required|array|max:500',
            'keys.*' => 'required|string|max:150',
        ]);

        $userId = $this->currentUserId();
        $now = now();

        $rows = collect($data['keys'])
            ->unique()
            ->map(fn ($key) => [
                'user_id' => $userId,
                'ack_key' => $key,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if (!empty($rows)) {
            DB::table('admin_notification_acks')->insertOrIgnore($rows);
        }

        return response()->json([
            'success' => true,
            'stored' => count($rows),
        ]);
    }

    /**
     * POST /api/admin/notification-acks/clear
     * Removes ALL acknowledged keys for the current admin user.
     */
    public function clearAll(Request $request)
    {
        AdminNotificationAck::where('user_id', $this->currentUserId())->delete();

        return response()->json(['success' => true]);
    }

    private function currentUserId(): int
    {
        return (int) (auth()->id() ?? 0);
    }
}