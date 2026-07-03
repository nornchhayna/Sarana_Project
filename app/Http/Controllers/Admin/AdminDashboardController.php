<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Compile the $stats array expected by the view
        $stats = [
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'total_users' => User::where('role', 'customer')->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'unread_contacts' => Contact::where('status', 'unread')->count(),
        ];

        // 2. Recent Orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 3. Monthly Sales for the Chart (Groups by month for the current year)
        $monthlySales = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', date('Y'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('month')
            ->get();

        // 4. Top Products (Requires the 'orderItems' relationship on the Product model)
        // If this throws an error, make sure you have public function orderItems() in Product.php
        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'monthlySales', 'topProducts'));
    }
}
