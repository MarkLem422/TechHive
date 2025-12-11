<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()?->role !== 'seller') {
            return redirect('/')->with('error', 'Access denied.');
        }

        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
            'total_revenue' => Order::sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => \App\Models\Variation::where('stock_quantity', '<', 5)->count(),
        ];

        $recent_orders = Order::with('customer')
            ->orderBy('order_date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_orders'));
    }
}

