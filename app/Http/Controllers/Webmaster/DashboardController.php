<?php

namespace App\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Webmaster-specific stats
        $stats = [
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'total_users' => User::where('role', 'customer')->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'unread_contacts' => Contact::where('status', 'unread')->count(),
        ];

        // Recent Orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Monthly Sales for the Chart
        $monthlySales = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', date('Y'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('month')
            ->get();

        // Top Products
        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        return view('webmaster.Dashboard', compact('stats', 'recentOrders', 'monthlySales', 'topProducts'));
    }
}
