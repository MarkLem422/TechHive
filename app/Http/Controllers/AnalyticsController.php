<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Date range filter - default to all time if no dates provided
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // If no dates provided, get all orders (set a very early start date)
        if (!$startDate) {
            $minDate = Order::min('order_date');
            if ($minDate) {
                $startDate = is_string($minDate) ? date('Y-m-d', strtotime($minDate)) : $minDate->format('Y-m-d');
            } else {
                $startDate = now()->subYear()->format('Y-m-d');
            }
        }
        if (!$endDate) {
            $endDate = now()->addDay()->format('Y-m-d'); // Add one day to include today
        }

        // Total Sales - only count delivered orders as completed sales
        $totalSales = Order::whereRaw("DATE(order_date) >= ?", [$startDate])
            ->whereRaw("DATE(order_date) <= ?", [$endDate])
            ->where('status', 'delivered')
            ->sum('total_amount');

        // Total Orders - count all non-cancelled orders
        $totalOrders = Order::whereRaw("DATE(order_date) >= ?", [$startDate])
            ->whereRaw("DATE(order_date) <= ?", [$endDate])
            ->where('status', '!=', 'cancelled')
            ->count();

        // Completed Orders (delivered only)
        $completedOrders = Order::whereRaw("DATE(order_date) >= ?", [$startDate])
            ->whereRaw("DATE(order_date) <= ?", [$endDate])
            ->where('status', 'delivered')
            ->count();

        // Orders by Status
        $ordersByStatus = Order::whereRaw("DATE(order_date) >= ?", [$startDate])
            ->whereRaw("DATE(order_date) <= ?", [$endDate])
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as revenue'))
            ->groupBy('status')
            ->get();

        // Best Selling Products - only count delivered orders
        $bestSellingProducts = DB::table('order_product')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->whereRaw("DATE(orders.order_date) >= ?", [$startDate])
            ->whereRaw("DATE(orders.order_date) <= ?", [$endDate])
            ->where('orders.status', 'delivered')
            ->select(
                'products.id',
                'products.product_name',
                DB::raw('sum(order_product.quantity) as total_quantity'),
                DB::raw('sum(order_product.quantity * order_product.price_at_purchase) as total_revenue')
            )
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Top Suppliers (based on products sold) - only count delivered orders
        $topSuppliers = DB::table('suppliers')
            ->join('stocks', 'suppliers.id', '=', 'stocks.supplier_id')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->whereRaw("DATE(orders.order_date) >= ?", [$startDate])
            ->whereRaw("DATE(orders.order_date) <= ?", [$endDate])
            ->where('orders.status', 'delivered')
            ->select(
                'suppliers.id',
                'suppliers.supplier_name',
                'suppliers.contact',
                'suppliers.phone',
                DB::raw('sum(order_product.quantity) as total_quantity_sold'),
                DB::raw('sum(order_product.quantity * order_product.price_at_purchase) as total_revenue')
            )
            ->groupBy('suppliers.id', 'suppliers.supplier_name', 'suppliers.contact', 'suppliers.phone')
            ->orderByDesc('total_quantity_sold')
            ->limit(10)
            ->get();

        // Revenue by Day (for chart) - only count delivered orders
        $revenueByDay = Order::whereRaw("DATE(order_date) >= ?", [$startDate])
            ->whereRaw("DATE(order_date) <= ?", [$endDate])
            ->where('status', 'delivered')
            ->select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('sum(total_amount) as revenue'),
                DB::raw('count(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Low Stock Products
        $lowStockProducts = Variation::where('stock_quantity', '<=', 10)
            ->with('product')
            ->get();

        return view('analytics.index', compact(
            'totalSales',
            'totalOrders',
            'completedOrders',
            'ordersByStatus',
            'bestSellingProducts',
            'topSuppliers',
            'revenueByDay',
            'lowStockProducts',
            'startDate',
            'endDate'
        ))->with('request', $request);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:products,suppliers,orders',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        switch ($request->report_type) {
            case 'products':
                $data = DB::table('order_product')
                    ->join('orders', 'order_product.order_id', '=', 'orders.id')
                    ->join('products', 'order_product.product_id', '=', 'products.id')
                    ->whereRaw("DATE(orders.order_date) >= ?", [$startDate])
                    ->whereRaw("DATE(orders.order_date) <= ?", [$endDate])
                    ->where('orders.status', 'delivered')
                    ->select(
                        'products.id',
                        'products.product_name',
                        DB::raw('sum(order_product.quantity) as total_quantity'),
                        DB::raw('sum(order_product.quantity * order_product.price_at_purchase) as total_revenue')
                    )
                    ->groupBy('products.id', 'products.product_name')
                    ->orderByDesc('total_quantity')
                    ->get();
                break;

            case 'suppliers':
                $data = DB::table('suppliers')
                    ->join('stocks', 'suppliers.id', '=', 'stocks.supplier_id')
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->join('order_product', 'products.id', '=', 'order_product.product_id')
                    ->join('orders', 'order_product.order_id', '=', 'orders.id')
                    ->whereRaw("DATE(orders.order_date) >= ?", [$startDate])
                    ->whereRaw("DATE(orders.order_date) <= ?", [$endDate])
                    ->where('orders.status', 'delivered')
                    ->select(
                        'suppliers.id',
                        'suppliers.supplier_name',
                        'suppliers.contact',
                        'suppliers.phone',
                        DB::raw('sum(order_product.quantity) as total_quantity_sold'),
                        DB::raw('sum(order_product.quantity * order_product.price_at_purchase) as total_revenue')
                    )
                    ->groupBy('suppliers.id', 'suppliers.supplier_name', 'suppliers.contact', 'suppliers.phone')
                    ->orderByDesc('total_quantity_sold')
                    ->get();
                break;

            case 'orders':
                $data = Order::whereRaw("DATE(order_date) >= ?", [$startDate])
                    ->whereRaw("DATE(order_date) <= ?", [$endDate])
                    ->with(['customer', 'products'])
                    ->orderByDesc('order_date')
                    ->get();
                break;
        }

        // Generate CSV
        $filename = $request->report_type . '_report_' . $startDate . '_to_' . $endDate . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $request) {
            $file = fopen('php://output', 'w');

            if ($request->report_type === 'products') {
                fputcsv($file, ['Product ID', 'Product Name', 'Total Quantity Sold', 'Total Revenue']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->id,
                        $row->product_name,
                        $row->total_quantity,
                        number_format($row->total_revenue, 2),
                    ]);
                }
            } elseif ($request->report_type === 'suppliers') {
                fputcsv($file, ['Supplier ID', 'Supplier Name', 'Contact', 'Phone', 'Total Quantity Sold', 'Total Revenue']);
                foreach ($data as $supplier) {
                    fputcsv($file, [
                        $supplier->id,
                        $supplier->supplier_name,
                        $supplier->contact ?? 'N/A',
                        $supplier->phone ?? 'N/A',
                        $supplier->total_quantity_sold,
                        number_format($supplier->total_revenue, 2),
                    ]);
                }
            } elseif ($request->report_type === 'orders') {
                fputcsv($file, ['Order ID', 'Customer', 'Date', 'Status', 'Total Amount']);
                foreach ($data as $order) {
                    fputcsv($file, [
                        $order->id,
                        $order->customer ? $order->customer->first_name . ' ' . $order->customer->last_name : 'Guest',
                        $order->order_date->format('Y-m-d H:i:s'),
                        $order->status,
                        number_format($order->total_amount, 2),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
