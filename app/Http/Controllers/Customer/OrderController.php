<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your orders.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();
        
        $activeStatus = $request->get('status', 'all');

        if (!$customer) {
            $orders = collect();
        } else {
            $query = Order::where('customer_id', $customer->id)
                ->with(['products' => function($query) {
                    $query->withPivot('quantity', 'price_at_purchase', 'variation_id');
                }]);

            if ($activeStatus && $activeStatus !== 'all') {
                $query->where('status', $activeStatus);
            }

            // Search by order ID
            if ($request->has('search') && $request->search) {
                $query->where('id', 'like', '%' . $request->search . '%');
            }

            $orders = $query->orderByDesc('order_date')->paginate(10)->withQueryString();
        }

        if ($request->ajax()) {
            return view('customer.orders.partials.list', compact('orders'));
        }

        return view('customer.orders.index', compact('orders', 'activeStatus'));
    }

    public function show(Order $order)
    {
        // Verify order belongs to customer if logged in
        if (Auth::check()) {
            $customer = Customer::where('email', Auth::user()->email)->first();
            if ($customer && $order->customer_id != $customer->id) {
                return redirect()->route('orders.index')->with('error', 'Order not found.');
            }
        }
        // Allow viewing order without auth (for order confirmation after checkout)

        $order->load(['products' => function($query) {
            $query->withPivot('quantity', 'price_at_purchase', 'variation_id');
        }, 'customer']);

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to manage orders.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();
        if (!$customer || $order->customer_id != $customer->id) {
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }

        if (in_array($order->status, ['shipped', 'delivered', 'cancelled'])) {
            return back()->with('error', 'You can only cancel orders that are not yet shipped.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully.');
    }
}
