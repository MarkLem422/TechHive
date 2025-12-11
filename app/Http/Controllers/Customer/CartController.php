<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $variation = Variation::with([
                'product.primaryImage',
                'product.productImages',
                'variantImages',
            ])->find($item['variation_id']);
            if ($variation) {
                $itemTotal = $variation->price * $item['quantity'];
                $subtotal += $itemTotal;

                $variantImage = $variation->variantImages->first();
                $productPrimary = $variation->product->primaryImage;
                $productFallback = $variation->product->productImages->first();
                $imagePath = $variantImage->image_path
                    ?? $productPrimary->image_path
                    ?? $productFallback->image_path
                    ?? null;
                $imageUrl = $imagePath ? Storage::url($imagePath) : 'https://placehold.co/300x200?text=No+Image';
                
                $cartItems[] = [
                    'key' => $key,
                    'variation' => $variation,
                    'product' => $variation->product,
                    'image_url' => $imageUrl,
                    'quantity' => $item['quantity'],
                    'price' => $variation->price,
                    'total' => $itemTotal,
                ];
            }
        }

        $tax = $subtotal * 0.12; // 12% tax
        $shipping = 100; // Fixed shipping cost
        $total = $subtotal + $tax + $shipping;

        return view('customer.cart.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_id' => 'required|exists:variations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variation = Variation::findOrFail($request->variation_id);
        
        // Check stock availability
        if ($variation->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock. Available: ' . $variation->stock_quantity);
        }

        // Verify variation belongs to product
        if ($variation->product_id != $request->product_id) {
            return back()->with('error', 'Invalid variation for this product.');
        }

        $cart = session()->get('cart', []);
        $key = $request->product_id . '_' . $request->variation_id;

        if (isset($cart[$key])) {
            $newQuantity = $cart[$key]['quantity'] + $request->quantity;
            if ($variation->stock_quantity < $newQuantity) {
                return back()->with('error', 'Insufficient stock. Available: ' . $variation->stock_quantity);
            }
            $cart[$key]['quantity'] = $newQuantity;
        } else {
            $cart[$key] = [
                'product_id' => $request->product_id,
                'variation_id' => $request->variation_id,
                'quantity' => $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return back()->with('error', 'Item not found in cart.');
        }

        $variation = Variation::findOrFail($cart[$key]['variation_id']);
        
        if ($variation->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock. Available: ' . $variation->stock_quantity);
        }

        $cart[$key]['quantity'] = $request->quantity;
        session()->put('cart', $cart);

        return back()->with('success', 'Cart updated!');
    }

    public function remove($key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
            return back()->with('success', 'Item removed from cart!');
        }

        return back()->with('error', 'Item not found in cart.');
    }
}
