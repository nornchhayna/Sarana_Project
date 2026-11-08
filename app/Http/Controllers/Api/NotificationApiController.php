<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = collect();

        // 1. Database notifications if present
        try {
            foreach ($user->notifications()->latest()->take(20)->get() as $dbNotif) {
                $data = is_array($dbNotif->data) ? $dbNotif->data : (json_decode((string) $dbNotif->data, true) ?? []);
                $notifications->push([
                    'id' => (string) $dbNotif->id,
                    'type' => $data['type'] ?? 'system',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? $data['body'] ?? '',
                    'body' => $data['message'] ?? $data['body'] ?? '',
                    'is_read' => $dbNotif->read_at !== null,
                    'read_at' => $dbNotif->read_at?->toIso8601String(),
                    'data' => $data,
                    'created_at' => $dbNotif->created_at?->toIso8601String(),
                ]);
            }
        } catch (\Throwable $e) {
            // Ignore if notifications table is unavailable
        }

        // 2. Dynamic order status notifications
        try {
            $orders = Order::where('user_id', $user->id)
                ->latest('updated_at')
                ->take(10)
                ->get();

            foreach ($orders as $order) {
                $statusText = match ($order->status) {
                    'confirmed' => "Payment verified for order #{$order->order_number}. We are preparing your items!",
                    'processing' => "Your order #{$order->order_number} is now being processed.",
                    'shipped' => "Great news! Your order #{$order->order_number} has been shipped.",
                    'delivered' => "Your order #{$order->order_number} has been delivered. Enjoy your purchase!",
                    'cancelled' => "Your order #{$order->order_number} was cancelled.",
                    default => "Order #{$order->order_number} has been placed (\${$order->total}).",
                };

                $notifications->push([
                    'id' => 'order-' . $order->id,
                    'type' => 'order',
                    'title' => 'Order ' . ucfirst($order->status) . ' (#' . $order->order_number . ')',
                    'message' => $statusText,
                    'body' => $statusText,
                    'is_read' => in_array($order->status, ['delivered', 'cancelled'], true),
                    'read_at' => in_array($order->status, ['delivered', 'cancelled'], true) ? $order->updated_at?->toIso8601String() : null,
                    'data' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                    ],
                    'created_at' => ($order->updated_at ?? $order->created_at)?->toIso8601String(),
                ]);
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // 3. Active promotions notifications
        try {
            $promotions = Promotion::where('is_active', true)
                ->latest()
                ->take(3)
                ->get();

            foreach ($promotions as $promo) {
                $desc = $promo->description ?: "Use code {$promo->code} to save on your next order!";
                $notifications->push([
                    'id' => 'promo-' . $promo->id,
                    'type' => 'promotion',
                    'title' => 'Special Offer: ' . $promo->name,
                    'message' => $desc,
                    'body' => $desc,
                    'is_read' => false,
                    'read_at' => null,
                    'data' => [
                        'promotion_id' => $promo->id,
                        'code' => $promo->code,
                    ],
                    'created_at' => $promo->created_at?->toIso8601String() ?? now()->toIso8601String(),
                ]);
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $sorted = $notifications->sortByDesc('created_at')->values();
        $unreadCount = $sorted->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'notifications' => $sorted,
            'unread_count' => $unreadCount,
            'data' => $sorted,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = 0;

        try {
            $count += $user->unreadNotifications()->count();
        } catch (\Throwable $e) {
            // Ignore
        }

        try {
            $count += Order::where('user_id', $user->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->count();
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'count' => $count,
            'unread_count' => $count,
            'data' => ['count' => $count, 'unread_count' => $count],
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        try {
            $notif = $request->user()->notifications()->where('id', $id)->first();
            $notif?->markAsRead();
        } catch (\Throwable $e) {
            // Ignore for synthetic order/promo notifications
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $request->user()->unreadNotifications->markAsRead();
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $request->user()->notifications()->where('id', $id)->delete();
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }
}
