<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Variation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'products' => function($q) {
            $q->withPivot('quantity', 'price_at_purchase', 'variation_id');
        }]);

        $activeStatus = $request->get('status', 'all');
        if ($activeStatus && $activeStatus !== 'all') {
            $query->where('status', $activeStatus);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order ID or customer name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderByDesc('order_date')->paginate(15);

        // Get low stock products for notifications
        $lowStockVariations = Variation::where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->with('product')
            ->get();

        $outOfStockVariations = Variation::where('stock_quantity', '<=', 0)
            ->with('product')
            ->get();

        if ($request->ajax()) {
            return view('orders.partials.list', compact('orders'));
        }

        return view('orders.index', compact('orders', 'lowStockVariations', 'outOfStockVariations', 'activeStatus'));
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'products' => function($query) {
                $query->withPivot('quantity', 'price_at_purchase', 'variation_id');
            }
        ]);

        // Get variation details for each product
        $orderItems = [];
        foreach ($order->products as $product) {
            $variation = null;
            if ($product->pivot->variation_id) {
                $variation = Variation::with('product')->find($product->pivot->variation_id);
            }
            
            $orderItems[] = [
                'product' => $product,
                'variation' => $variation,
                'quantity' => $product->pivot->quantity,
                'price' => $product->pivot->price_at_purchase,
                'total' => $product->pivot->price_at_purchase * $product->pivot->quantity,
            ];
        }

        return view('orders.show', compact('order', 'orderItems'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Check stock availability when changing to shipped
        if ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
            foreach ($order->products as $product) {
                if ($product->pivot->variation_id) {
                    $variation = Variation::find($product->pivot->variation_id);
                    if ($variation && $variation->stock_quantity < $product->pivot->quantity) {
                        return back()->with('error', 'Insufficient stock for ' . $product->product_name . '. Available: ' . $variation->stock_quantity);
                    }
                }
            }
        }

        // Update payment status if order is paid
        if ($newStatus === 'paid') {
            $order->update([
                'status' => $newStatus,
                'payment_status' => 'paid',
            ]);
        } else {
            $order->update([
                'status' => $newStatus,
            ]);
        }

        // Send notifications based on status
        if ($newStatus === 'shipped') {
            NotificationService::notifyOrderShipped($order);
        } elseif ($newStatus === 'delivered') {
            NotificationService::notifyOrderDelivered($order);
        } elseif ($newStatus === 'paid') {
            NotificationService::notifyOrderPaid($order);
        } elseif ($newStatus === 'cancelled') {
            NotificationService::notifyOrderCancelled($order);
        }

        return back()->with('success', 'Order status updated successfully!');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        // If payment is confirmed, update order status to paid
        if ($request->payment_status === 'paid' && $order->status === 'pending') {
            $order->update([
                'status' => 'paid',
            ]);

            NotificationService::notifyOrderPaid($order);
        }

        return back()->with('success', 'Payment status updated successfully!');
    }
}

