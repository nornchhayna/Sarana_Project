<?php

namespace App\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->get('year', date('Y'));

        // Available years for filter dropdown
        $dbYears = Order::selectRaw('YEAR(created_at) as yr')
            ->whereNotNull('created_at')
            ->distinct()
            ->pluck('yr')
            ->map(fn ($y) => (int) $y)
            ->toArray();

        $availableYears = array_unique(array_merge([date('Y'), date('Y') - 1], $dbYears));
        rsort($availableYears);

        // Core Revenue & Order Metrics
        // Consider delivered & confirmed orders as successful sales
        $totalRevenue = (float) Order::whereIn('status', ['delivered', 'confirmed'])->sum('total');
        $totalOrders = Order::count();
        $newCustomers = User::where('role', 'customer')->count();

        // Stock stats
        $variantLowStock = ProductVariant::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $singleLowStock = \App\Models\Stock::whereNotIn('product_id', ProductVariant::select('product_id'))
            ->where('quantity', '<=', 10)->where('quantity', '>', 0)->count();
        $lowStockCount = $variantLowStock + $singleLowStock;

        $variantOutOfStock = ProductVariant::where('stock', 0)->count();
        $singleOutOfStock = \App\Models\Stock::whereNotIn('product_id', ProductVariant::select('product_id'))
            ->where('quantity', 0)->count();
        $outOfStockCount = $variantOutOfStock + $singleOutOfStock;

        // Calculate deltas (vs last month)
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        $thisMonthStart = now()->startOfMonth();

        $thisMonthRevenue = (float) Order::whereIn('status', ['delivered', 'confirmed'])
            ->where('created_at', '>=', $thisMonthStart)
            ->sum('total');

        $lastMonthRevenue = (float) Order::whereIn('status', ['delivered', 'confirmed'])
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total');

        $thisMonthOrders = Order::where('created_at', '>=', $thisMonthStart)->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $thisMonthCustomers = User::where('role', 'customer')
            ->where('created_at', '>=', $thisMonthStart)
            ->count();
        $lastMonthCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();

        $revenueDelta = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100.0 : 0.0);

        $ordersDelta = $lastMonthOrders > 0
            ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : ($thisMonthOrders > 0 ? 100.0 : 0.0);

        $customersDelta = $lastMonthCustomers > 0
            ? round((($thisMonthCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
            : ($thisMonthCustomers > 0 ? 100.0 : 0.0);

        $lowStockDelta = 0;

        // Order Status Counts
        $orderStatusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // Monthly Sales for the Chart for the selected year
        $monthlySalesQuery = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as orders')
            ->whereYear('created_at', $selectedYear)
            ->where('status', '!=', 'cancelled')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyDataMap = [];
        foreach ($monthlySalesQuery as $row) {
            $monthlyDataMap[(int) $row->month] = [
                'revenue' => (float) $row->revenue,
                'orders' => (int) $row->orders,
            ];
        }

        $rawChartPoints = collect();
        for ($m = 1; $m <= 12; $m++) {
            $rev = $monthlyDataMap[$m]['revenue'] ?? 0;
            $ord = $monthlyDataMap[$m]['orders'] ?? 0;
            $monthLabel = Carbon::create()->month($m)->format('M');

            $rawChartPoints->push([
                'label' => $monthLabel,
                'revenue_raw' => $rev,
                'orders_raw' => $ord,
            ]);
        }

        $maxRev = $rawChartPoints->max('revenue_raw') ?: 1;
        $maxOrd = $rawChartPoints->max('orders_raw') ?: 1;

        $chartData = $rawChartPoints->map(function ($point) use ($maxRev, $maxOrd) {
            $revPct = $maxRev > 0 ? ($point['revenue_raw'] / $maxRev) * 100 : 0;
            $ordPct = $maxOrd > 0 ? ($point['orders_raw'] / $maxOrd) * 100 : 0;

            return [
                'label' => $point['label'],
                'revenue' => max(4, round($revPct)),
                'orders' => max(4, round($ordPct)),
                'revenue_raw' => $point['revenue_raw'],
                'orders_raw' => $point['orders_raw'],
            ];
        });

        $yearTotalRevenue = (float) $rawChartPoints->sum('revenue_raw');
        $yearTotalOrders = (int) $rawChartPoints->sum('orders_raw');
        $avgOrderValue = $yearTotalOrders > 0 ? ($yearTotalRevenue / $yearTotalOrders) : 0;

        // Recent Orders
        $recentOrders = Order::with(['user', 'payment'])
            ->withCount('items')
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($order) {
                return (object) [
                    'id' => $order->id,
                    'formatted_id' => $order->order_number ?: ('ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT)),
                    'customer_name' => $order->shipping_name ?: ($order->user ? $order->user->name : 'Guest Customer'),
                    'customer_email' => $order->user?->email,
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'items_count' => $order->items_count,
                    'created_at' => $order->created_at ? $order->created_at->format('M d, H:i') : 'Recently',
                    'time_ago' => $order->created_at ? $order->created_at->diffForHumans() : 'Just now',
                ];
            });

        // Top Products
        $topProducts = Product::with(['category', 'variants'])
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(6)
            ->get()
            ->map(function ($product) {
                $totalStock = $product->total_stock;
                $price = $product->current_price > 0 ? $product->current_price : ($product->base_price ?: 0);

                return (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category ? $product->category->name : 'General',
                    'price' => (float) $price,
                    'stock' => (int) $totalStock,
                    'stock_max' => max(50, (int) $totalStock),
                    'total_sold' => (int) ($product->total_sold ?? $product->order_items_count ?? 0),
                    'image_url' => $product->main_image_url ?: $product->image_url,
                ];
            });

        // Urgent Low Stock Products
        $criticalStockItems = ProductVariant::with(['product', 'color', 'phoneStorage'])
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(5)
            ->get();

        // Real Dynamic Activity Feed
        $activityEvents = collect();

        // 1. Orders
        foreach (Order::with('user')->latest()->take(4)->get() as $ord) {
            $cust = $ord->shipping_name ?: ($ord->user?->name ?: 'Customer');
            $activityEvents->push([
                'timestamp' => $ord->created_at,
                'color' => '#5B6EF5',
                'icon' => 'fa-cart-shopping',
                'url' => route('webmaster.orders.show', $ord->id),
                'text' => '<strong>' . e($cust) . '</strong> placed order #' . e($ord->order_number ?: $ord->id) . ' for $' . number_format($ord->total, 2),
                'time' => $ord->created_at ? $ord->created_at->diffForHumans() : 'Just now',
            ]);
        }

        // 2. Paid Payments
        foreach (Payment::with('order')->where('status', 'paid')->latest()->take(3)->get() as $pm) {
            $activityEvents->push([
                'timestamp' => $pm->paid_at ?: $pm->created_at,
                'color' => '#1F9D63',
                'icon' => 'fa-circle-check',
                'url' => route('webmaster.payments.show', $pm->id),
                'text' => 'Payment of <strong>$' . number_format($pm->amount, 2) . '</strong> verified for order #' . e($pm->order?->order_number ?: $pm->order_id),
                'time' => ($pm->paid_at ?: $pm->created_at) ? ($pm->paid_at ?: $pm->created_at)->diffForHumans() : 'Recently',
            ]);
        }

        // 3. Low stock alerts
        foreach (ProductVariant::with('product')->where('stock', '<=', 3)->take(2)->get() as $pv) {
            $pName = $pv->product?->name ?: 'Item';
            $activityEvents->push([
                'timestamp' => $pv->updated_at,
                'color' => '#F5A623',
                'icon' => 'fa-triangle-exclamation',
                'url' => route('webmaster.stock.index', ['stock_status' => 'low']),
                'text' => '<strong>' . e($pName) . '</strong> stock critically low (' . $pv->stock . ' remaining)',
                'time' => $pv->updated_at ? $pv->updated_at->diffForHumans() : 'Recently',
            ]);
        }

        // 4. Customer reviews
        foreach (Review::with(['user', 'product'])->latest()->take(2)->get() as $rev) {
            $userName = $rev->user?->name ?: 'Customer';
            $prodName = $rev->product?->name ?: 'Product';
            $activityEvents->push([
                'timestamp' => $rev->created_at,
                'color' => '#8B5CF6',
                'icon' => 'fa-star',
                'url' => route('webmaster.reviews.show', $rev->id),
                'text' => '<strong>' . e($userName) . '</strong> left a ' . $rev->rating . '★ review on ' . e($prodName),
                'time' => $rev->created_at ? $rev->created_at->diffForHumans() : 'Recently',
            ]);
        }

        // 5. Contact inquiries
        foreach (Contact::latest()->take(2)->get() as $contact) {
            $activityEvents->push([
                'timestamp' => $contact->created_at,
                'color' => '#06B6D4',
                'icon' => 'fa-envelope',
                'url' => route('webmaster.contacts.show', $contact->id),
                'text' => 'New inquiry from <strong>' . e($contact->name) . '</strong>: ' . e($contact->subject ?: 'General Inquiry'),
                'time' => $contact->created_at ? $contact->created_at->diffForHumans() : 'Recently',
            ]);
        }

        // Sort combined activity chronologically
        $activity = $activityEvents->sortByDesc('timestamp')->take(6)->values();

        // Admin-compatible $stats array
        $stats = [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'pending_orders' => $orderStatusCounts['pending'] ?? 0,
            'total_products' => Product::where('is_active', true)->count(),
            'total_users' => $newCustomers,
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'unread_contacts' => Contact::where('status', 'unread')->count(),
        ];

        // Admin-compatible $monthlySales format for Chart.js
        $monthlySales = $monthlySalesQuery->map(function ($row) {
            return (object) [
                'month' => (int) $row->month,
                'revenue' => (float) $row->revenue,
                'orders' => (int) $row->orders,
            ];
        });

        // Top products formatted for AdminLTE list-group & table
        $topProductsModels = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        // Recent orders collection for AdminLTE table
        $recentOrdersModels = Order::with('user')
            ->latest()
            ->take(6)
            ->get();

        // Fallback activity if store is brand new
        if ($activity->isEmpty()) {
            $activity->push([
                'timestamp' => now(),
                'color' => '#5B6EF5',
                'icon' => 'fa-circle-info',
                'url' => route('webmaster.dashboard'),
                'text' => '<strong>System</strong> Dashboard initialized successfully',
                'time' => 'Just now',
            ]);
        }

        return view('webmaster.Dashboard', compact(
            'stats',
            'monthlySales',
            'topProductsModels',
            'recentOrdersModels',
            'totalRevenue',
            'revenueDelta',
            'totalOrders',
            'ordersDelta',
            'newCustomers',
            'customersDelta',
            'lowStockCount',
            'outOfStockCount',
            'lowStockDelta',
            'orderStatusCounts',
            'chartData',
            'selectedYear',
            'availableYears',
            'yearTotalRevenue',
            'yearTotalOrders',
            'avgOrderValue',
            'recentOrders',
            'topProducts',
            'criticalStockItems',
            'activity'
        ));
    }
}
