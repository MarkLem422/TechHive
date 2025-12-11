<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return back()->with('error', 'Please login to submit a review.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $customer = Customer::where('email', Auth::user()->email)->first();
        
        if (!$customer) {
            return back()->with('error', 'Customer not found.');
        }

        // Check if customer has already reviewed this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('customer_id', $customer->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Verify order belongs to customer if order_id is provided
        if ($request->order_id) {
            $order = Order::find($request->order_id);
            if (!$order || $order->customer_id !== $customer->id) {
                return back()->with('error', 'Invalid order.');
            }
        }

        Review::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approve for now, can be changed to require admin approval
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }
}
