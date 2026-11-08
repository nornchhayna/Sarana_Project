<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Reports Hub Index - Overview of all key business metrics
     */
    public function index(): View
    {
        $totalRevenue = (float) Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])->sum('total');
        $totalOrders = Order::count();
        $totalStockValue = (float) ProductVariant::selectRaw('SUM(stock * price) as val')->value('val');
        $totalItemsInStock = (int) ProductVariant::sum('stock');
        $lowStockCount = ProductVariant::where('stock', '>', 0)->where('stock', '<=', 10)->count();
        $totalCogs = (float) PurchaseOrder::where('status', 'received')->sum('total_amount');
        $grossProfit = $totalRevenue - $totalCogs;

        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalOrders',
            'totalStockValue',
            'totalItemsInStock',
            'lowStockCount',
            'totalCogs',
            'grossProfit'
        ));
    }

    /**
     * Sales & Orders Dashboard - Overview of sales performance & order metrics
     */
    public function salesDashboard(Request $request): View
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $statusFilter = $request->get('status', 'all');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Base query with date range
        $ordersQuery = Order::whereBetween('orders.created_at', [$start, $end]);

        if ($statusFilter !== 'all') {
            $ordersQuery->where('orders.status', $statusFilter);
        }

        // Summary Metrics
        $totalOrders = (clone $ordersQuery)->count();

        // Revenue from confirmed/delivered orders (non-cancelled)
        $totalRevenue = Order::whereBetween('orders.created_at', [$start, $end])
            ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('orders.total');

        $deliveredRevenue = Order::whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', 'delivered')
            ->sum('orders.total');

        $totalItemsSold = OrderItem::whereHas('order', function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
        })->sum('quantity');

        $deliveredOrdersCount = Order::whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', 'delivered')
            ->count();

        $cancelledOrdersCount = Order::whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', 'cancelled')
            ->count();

        $pendingOrdersCount = Order::whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', 'pending')
            ->count();

        $confirmedOrdersCount = Order::whereBetween('orders.created_at', [$start, $end])
            ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->count();

        $averageOrderValue = $confirmedOrdersCount > 0 ? $totalRevenue / $confirmedOrdersCount : 0;
        $cancellationRate = $totalOrders > 0 ? ($cancelledOrdersCount / $totalOrders) * 100 : 0;

        // Sales by Status
        $salesByStatus = Order::whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('orders.status, COUNT(*) as count, SUM(orders.total) as total')
            ->groupBy('orders.status')
            ->get()
            ->keyBy('status')
            ->map(function ($item) {
                $item->total = $item->total ?? 0;
                return $item;
            });

        // Daily Sales & Order Trend
        $dailySales = Order::whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('DATE(orders.created_at) as date,
                         COUNT(*) as total_orders,
                         SUM(CASE WHEN orders.status IN ("confirmed", "processing", "shipped", "delivered") THEN 1 ELSE 0 END) as valid_orders,
                         SUM(CASE WHEN orders.status IN ("confirmed", "processing", "shipped", "delivered") THEN orders.total ELSE 0 END) as revenue,
                         SUM(CASE WHEN orders.status = "cancelled" THEN 1 ELSE 0 END) as cancelled_orders')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Top Selling Products
        $topProducts = OrderItem::whereHas('order', function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
        })
        ->selectRaw('product_id, product_name, SUM(quantity) as total_sold, SUM(unit_price * quantity) as total_revenue, AVG(unit_price) as avg_price')
        ->groupBy('product_id', 'product_name')
        ->orderByDesc('total_revenue')
        ->limit(10)
        ->get();

        // Sales by Category
        $salesByCategory = OrderItem::whereHas('order', function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
        })
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
        ->selectRaw('products.category_id, COALESCE(categories.name, "Uncategorized") as category_name, SUM(order_items.quantity) as total_sold, SUM(order_items.unit_price * order_items.quantity) as total_revenue')
        ->groupBy('products.category_id', 'categories.name')
        ->orderByDesc('total_revenue')
        ->get();

        // Sales by Payment Method
        $salesByPayment = Order::whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', '!=', 'cancelled')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->selectRaw('COALESCE(payments.method, "Pending / Other") as method, COUNT(DISTINCT orders.id) as count, SUM(orders.total) as total')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        // Recent Orders in Period
        $recentOrders = Order::whereBetween('orders.created_at', [$start, $end])
            ->with(['user', 'items', 'payments'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.reports.sales-dashboard', compact(
            'startDate',
            'endDate',
            'statusFilter',
            'totalRevenue',
            'deliveredRevenue',
            'totalOrders',
            'deliveredOrdersCount',
            'pendingOrdersCount',
            'cancelledOrdersCount',
            'totalItemsSold',
            'averageOrderValue',
            'cancellationRate',
            'salesByStatus',
            'dailySales',
            'topProducts',
            'salesByCategory',
            'salesByPayment',
            'recentOrders'
        ));
    }

    /**
     * Stock / Inventory Valuation Report
     */
    public function inventoryValuation(Request $request): View
    {
        // Total stock value ($)
        $totalStockValue = (float) ProductVariant::selectRaw('SUM(stock * price) as total_val')
            ->value('total_val');

        // Total items in stock
        $totalItems = (int) ProductVariant::sum('stock');
        $totalVariants = ProductVariant::count();
        $totalProducts = Product::count();

        // Low stock count (1 to 10 units)
        $lowStockCount = ProductVariant::where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->count();

        // Out of stock count (0 units)
        $outOfStockCount = ProductVariant::where('stock', 0)->count();

        // Adequate stock count (> 10 units)
        $inStockCount = ProductVariant::where('stock', '>', 10)->count();

        // Stock by category
        $stockByCategory = ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('products.category_id, COALESCE(categories.name, "Uncategorized") as category_name, COUNT(product_variants.id) as variant_count, SUM(product_variants.stock) as total_stock, SUM(product_variants.stock * product_variants.price) as total_value')
            ->groupBy('products.category_id', 'categories.name')
            ->orderByDesc('total_value')
            ->get();

        // Stock by brand
        $stockByBrand = ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->selectRaw('products.brand_id, COALESCE(brands.name, "No Brand") as brand_name, COUNT(product_variants.id) as variant_count, SUM(product_variants.stock) as total_stock, SUM(product_variants.stock * product_variants.price) as total_value')
            ->groupBy('products.brand_id', 'brands.name')
            ->orderByDesc('total_value')
            ->get();

        // Low stock items (1 to 10)
        $lowStockItems = ProductVariant::with(['product.brand', 'product.category', 'color', 'phoneStorage', 'phoneRam'])
            ->where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->limit(20)
            ->get();

        // Out of stock items (0)
        $outOfStockItems = ProductVariant::with(['product.brand', 'product.category', 'color', 'phoneStorage', 'phoneRam'])
            ->where('stock', 0)
            ->limit(20)
            ->get();

        // Top High value items
        $highValueItems = ProductVariant::with(['product.brand', 'color', 'phoneStorage', 'phoneRam'])
            ->selectRaw('product_variants.*, product_variants.stock * product_variants.price as stock_value')
            ->where('stock', '>', 0)
            ->orderByDesc('stock_value')
            ->limit(20)
            ->get();

        // Recent stock movements (audit ledger)
        $recentMovements = StockMovement::with(['variant.product', 'variant.color', 'variant.phoneStorage', 'createdBy'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.reports.inventory-valuation', compact(
            'totalStockValue',
            'totalItems',
            'totalVariants',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'inStockCount',
            'stockByCategory',
            'stockByBrand',
            'lowStockItems',
            'outOfStockItems',
            'highValueItems',
            'recentMovements'
        ));
    }

    /**
     * Product Performance Report
     */
    public function productPerformance(Request $request): View
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Product sales data
        $productPerformance = OrderItem::whereHas('order', function ($q) use ($start, $end) {
            $q->whereBetween('orders.created_at', [$start, $end])
                ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered']);
        })
        ->selectRaw('
            order_items.product_id,
            order_items.product_name,
            COUNT(DISTINCT order_items.order_id) as order_count,
            SUM(order_items.quantity) as total_sold,
            SUM(order_items.unit_price * order_items.quantity) as total_revenue,
            AVG(order_items.unit_price) as avg_price
        ')
        ->groupBy('order_items.product_id', 'order_items.product_name')
        ->orderByDesc('total_revenue')
        ->paginate(50);

        // Best sellers
        $bestSellers = OrderItem::whereHas('order', function ($q) use ($start, $end) {
            $q->whereBetween('orders.created_at', [$start, $end])
                ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered']);
        })
        ->selectRaw('order_items.product_id, order_items.product_name, SUM(order_items.quantity) as total_sold, SUM(order_items.unit_price * order_items.quantity) as revenue')
        ->groupBy('order_items.product_id', 'order_items.product_name')
        ->orderByDesc('total_sold')
        ->limit(10)
        ->get();

        // Slow moving variants (with stock but no sales)
        $worstSellers = ProductVariant::with(['product', 'color', 'phoneStorage'])
            ->where('stock', '>', 0)
            ->whereHas('product', function ($q) {
                $q->where('is_active', true)
                  ->whereDoesntHave('orderItems');
            })
            ->limit(10)
            ->get();

        return view('admin.reports.product-performance', compact(
            'startDate',
            'endDate',
            'productPerformance',
            'bestSellers',
            'worstSellers'
        ));
    }

    /**
     * Purchase Order Summary
     */
    public function purchaseOrderSummary(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // PO status summary
        $poStatusSummary = PurchaseOrder::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total_amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Open POs (ordered or partially_received)
        $openPOs = PurchaseOrder::whereIn('status', ['ordered', 'partially_received'])
            ->with('supplier')
            ->latest()
            ->get();

        // Total PO value this month
        $monthlyPOValue = PurchaseOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Total PO value in selected range
        $rangePOValue = PurchaseOrder::whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        // Supplier performance
        $supplierPerformance = PurchaseOrder::whereBetween('purchase_orders.created_at', [$start, $end])
            ->with('supplier')
            ->selectRaw('supplier_id, COUNT(*) as total_orders, SUM(total_amount) as total_spent')
            ->groupBy('supplier_id')
            ->orderByDesc('total_spent')
            ->get();

        return view('admin.reports.purchase-order-summary', compact(
            'startDate',
            'endDate',
            'poStatusSummary',
            'openPOs',
            'monthlyPOValue',
            'rangePOValue',
            'supplierPerformance'
        ));
    }

    /**
     * Financial Summary - Revenue, Cost of Goods Sold, Profit, Margin & Monthly Trends
     */
    public function financialSummary(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $year = (int) $request->get('year', now()->year);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Revenue from customer orders (confirmed, processing, shipped, delivered)
        $revenue = (float) Order::whereBetween('orders.created_at', [$start, $end])
            ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('orders.total');

        $deliveredRevenue = (float) Order::whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', 'delivered')
            ->sum('orders.total');

        // COGS (Cost of Goods Sold) - from received purchase orders
        $cogs = (float) PurchaseOrder::whereBetween('purchase_orders.created_at', [$start, $end])
            ->where('purchase_orders.status', 'received')
            ->sum('purchase_orders.total_amount');

        // Total purchase orders commitments (all POs created)
        $totalPurchaseCommitment = (float) PurchaseOrder::whereBetween('purchase_orders.created_at', [$start, $end])
            ->where('purchase_orders.status', '!=', 'cancelled')
            ->sum('purchase_orders.total_amount');

        // Gross profit & margin
        $grossProfit = $revenue - $cogs;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        // Orders metrics
        $totalOrders = Order::whereBetween('orders.created_at', [$start, $end])
            ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->count();

        $allOrdersCount = Order::whereBetween('orders.created_at', [$start, $end])->count();
        $avgOrderValue = $totalOrders > 0 ? $revenue / $totalOrders : 0;

        // Payment status breakdown (Cash flow visibility)
        $paymentBreakdown = Order::whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', '!=', 'cancelled')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->selectRaw('COALESCE(payments.method, "Pending") as method, COUNT(DISTINCT orders.id) as orders_count, SUM(orders.total) as total_amount')
            ->groupBy('method')
            ->get();

        // Monthly Financial Trend (Sales vs Purchases for the selected year)
        $monthlyRevenue = Order::whereYear('orders.created_at', $year)
            ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->selectRaw('MONTH(orders.created_at) as month, COUNT(*) as orders_count, SUM(orders.total) as revenue')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $monthlyPurchases = PurchaseOrder::whereYear('purchase_orders.created_at', $year)
            ->where('purchase_orders.status', 'received')
            ->selectRaw('MONTH(purchase_orders.created_at) as month, COUNT(*) as po_count, SUM(purchase_orders.total_amount) as expenses')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $monthlyTrend = collect();
        for ($m = 1; $m <= 12; $m++) {
            $revRow = $monthlyRevenue->get($m);
            $purRow = $monthlyPurchases->get($m);

            $mOrders = $revRow ? (int) $revRow->orders_count : 0;
            $mRev = $revRow ? (float) $revRow->revenue : 0.0;
            $mExp = $purRow ? (float) $purRow->expenses : 0.0;
            $mProfit = $mRev - $mExp;
            $mMargin = $mRev > 0 ? ($mProfit / $mRev) * 100 : 0.0;

            // Include month if there was activity or if month <= current month
            if ($year === (int) now()->year && $m > (int) now()->month && $mOrders === 0 && $mExp == 0) {
                continue;
            }

            $monthlyTrend->push((object) [
                'month' => $m,
                'month_name' => Carbon::create($year, $m, 1)->format('F'),
                'orders' => $mOrders,
                'revenue' => $mRev,
                'expenses' => $mExp,
                'profit' => $mProfit,
                'margin' => $mMargin,
            ]);
        }

        // Top Revenue Generating Orders in Period
        $topRevenueOrders = Order::whereBetween('orders.created_at', [$start, $end])
            ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->with(['user'])
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Top Purchase Orders in Period
        $topPurchaseOrders = PurchaseOrder::whereBetween('purchase_orders.created_at', [$start, $end])
            ->with(['supplier'])
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        return view('admin.reports.financial-summary', compact(
            'startDate',
            'endDate',
            'year',
            'revenue',
            'deliveredRevenue',
            'cogs',
            'totalPurchaseCommitment',
            'grossProfit',
            'grossMargin',
            'totalOrders',
            'allOrdersCount',
            'avgOrderValue',
            'paymentBreakdown',
            'monthlyTrend',
            'topRevenueOrders',
            'topPurchaseOrders'
        ));
    }
}
