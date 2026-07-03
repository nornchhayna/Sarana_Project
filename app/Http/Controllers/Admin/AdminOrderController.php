<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stock;
use App\Notifications\OrderShippedNotification;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->search.'%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        $statuses = self::STATUSES;

        $counts = collect(self::STATUSES)->mapWithKeys(fn ($s) => [
            $s => Order::where('status', $s)->count(),
        ]);

        return view('admin.orders.index', compact('orders', 'statuses', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.product',
            'items.color',
            'items.phoneStorage',
            'payment',
            'invoice',
            'promotion',
        ]);

        $statuses = self::STATUSES;

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', self::STATUSES),
            'note' => 'nullable|string|max:500',
        ]);

        $previousStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        // Send notifications on key transitions
        if ($validated['status'] === 'shipped' && $previousStatus !== 'shipped') {
            $order->user->notify(new OrderShippedNotification($order));
        }

        return back()->with('success', 'Order status updated to '.ucfirst($validated['status']).'.');
    }

    public function cancel(Order $order)
    {
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this order.');
        }

        $order->update(['status' => 'cancelled']);

        // Restore stock
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('color_id', $item->color_id)
                ->where('phone_storage_id', $item->phone_storage_id)
                ->first();

            if ($stock) {
                $stock->increment('quantity', $item->quantity);
            }
        }

        return back()->with('success', 'Order cancelled and stock restored.');
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        $csvData = "Order Number,Customer,Status,Total,Date\n";
        foreach ($orders as $order) {
            $csvData .= implode(',', [
                $order->order_number,
                '"'.$order->user->name.'"',
                $order->status,
                $order->total,
                $order->created_at->format('Y-m-d H:i'),
            ])."\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-'.now()->format('Ymd').'.csv"',
        ]);
    }
}
