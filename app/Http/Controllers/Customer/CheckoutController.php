<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Variation;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $variation = Variation::with('product')->find($item['variation_id']);
            if ($variation) {
                $itemTotal = $variation->price * $item['quantity'];
                $subtotal += $itemTotal;
                
                $cartItems[] = [
                    'key' => $key,
                    'variation' => $variation,
                    'product' => $variation->product,
                    'quantity' => $item['quantity'],
                    'price' => $variation->price,
                    'total' => $itemTotal,
                ];
            }
        }

        $tax = $subtotal * 0.12; // 12% tax
        $shipping = 100; // Fixed shipping cost
        $total = $subtotal + $tax + $shipping;

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cod,digital',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = 0;
        $cartItems = [];

        foreach ($cart as $key => $item) {
            $variation = Variation::with('product')->find($item['variation_id']);
            if ($variation) {
                // Check stock availability
                if ($variation->stock_quantity < $item['quantity']) {
                    return back()->with('error', 'Insufficient stock for ' . $variation->product->product_name . ' - ' . $variation->variation_name);
                }

                $itemTotal = $variation->price * $item['quantity'];
                $subtotal += $itemTotal;
                
                $cartItems[] = [
                    'variation' => $variation,
                    'product' => $variation->product,
                    'quantity' => $item['quantity'],
                    'price' => $variation->price,
                ];
            }
        }

        $tax = $subtotal * 0.12;
        $shipping = 100;
        $total = $subtotal + $tax + $shipping;

        DB::beginTransaction();
        try {
            // Get or create customer
            $customer = null;
            if (Auth::check()) {
                $customer = Customer::firstOrCreate(
                    ['email' => Auth::user()->email],
                    [
                        'first_name' => Auth::user()->name,
                        'last_name' => '',
                        'email' => Auth::user()->email,
                    ]
                );
            } else {
                $customer = Customer::firstOrCreate(
                    ['email' => $request->shipping_email],
                    [
                        'first_name' => $request->shipping_name,
                        'last_name' => '',
                        'email' => $request->shipping_email,
                        'phone' => $request->shipping_phone,
                        'address' => $request->shipping_address,
                    ]
                );
            }

            // Determine order and payment status based on payment method
            $paymentMethod = $request->payment_method;
            $orderStatus = 'pending';
            $paymentStatus = 'pending';

            if ($paymentMethod === 'digital') {
                // For digital payment, we'll process it and update status
                // This is a placeholder - integrate with Stripe/PayPal here
                $paymentStatus = 'processing';
            }

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_date' => now(),
                'total_amount' => $total,
                'status' => $orderStatus,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_cost' => $shipping,
                'tax' => $tax,
            ]);

            // Attach products and variations to order
            foreach ($cartItems as $item) {
                // Use direct DB insert to handle variation_id properly
                $insertData = [
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ];
                
                // Add variation_id if the column exists
                if (Schema::hasColumn('order_product', 'variation_id')) {
                    $insertData['variation_id'] = $item['variation']->id;
                }
                
                // Check if this product already exists in the order (due to primary key constraint)
                $existing = DB::table('order_product')
                    ->where('order_id', $order->id)
                    ->where('product_id', $item['product']->id)
                    ->first();
                
                if ($existing) {
                    // Update existing record if same product
                    DB::table('order_product')
                        ->where('order_id', $order->id)
                        ->where('product_id', $item['product']->id)
                        ->update([
                            'quantity' => $existing->quantity + $item['quantity'],
                            'price_at_purchase' => $item['price'], // Use latest price
                            'variation_id' => $insertData['variation_id'] ?? null,
                        ]);
                } else {
                    // Insert new record
                    DB::table('order_product')->insert($insertData);
                }

                // Update stock
                $item['variation']->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            // Handle digital payment processing
            if ($paymentMethod === 'digital') {
                // Placeholder for payment processing
                // In production, integrate with Stripe/PayPal here
                // For now, simulate successful payment
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                ]);
            }

            // Send notifications
            NotificationService::notifyOrderPlaced($order);

            // Clear cart
            session()->forget('cart');

            // Redirect to confirmation page
            return redirect()->route('checkout.confirmation', $order)->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'cart' => $cart,
            ]);
            // Show detailed error in development, generic in production
            $errorMessage = config('app.debug') 
                ? 'Failed to place order: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
                : 'Failed to place order. Please try again.';
            return back()->withInput()->with('error', $errorMessage);
        }
    }

    public function confirmation(Order $order)
    {
        $order->load(['products' => function($query) {
            $query->withPivot('quantity', 'price_at_purchase', 'variation_id');
        }, 'customer']);

        return view('customer.checkout.confirmation', compact('order'));
    }

    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_gateway' => 'required|in:stripe,paypal',
            'payment_token' => 'required_if:payment_gateway,stripe|string',
            'payment_id' => 'required_if:payment_gateway,paypal|string',
            'payer_id' => 'required_if:payment_gateway,paypal|string',
        ]);

        DB::beginTransaction();
        try {
            $result = null;

            if ($request->payment_gateway === 'stripe') {
                $result = PaymentService::processStripePayment($order, $request->payment_token);
            } elseif ($request->payment_gateway === 'paypal') {
                $result = PaymentService::processPayPalPayment($order, $request->payment_id, $request->payer_id);
            }

            if ($result && $result['success']) {
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                ]);

                // Send notification
                NotificationService::notifyOrderPlaced($order);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'order' => $order,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Payment failed. Please try again.',
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment processing error. Please try again.',
            ], 500);
        }
    }
}
