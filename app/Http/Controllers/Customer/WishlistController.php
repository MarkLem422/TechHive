<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your wishlist.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();
        
        if (!$customer) {
            $wishlists = collect();
        } else {
            $wishlists = Wishlist::where('customer_id', $customer->id)
                ->with(['product.category', 'variation'])
                ->latest()
                ->paginate(12);
        }

        return view('customer.wishlist.index', compact('wishlists'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login to add items to wishlist.'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_id' => 'nullable|exists:variations,id',
        ]);

        $customer = Customer::where('email', Auth::user()->email)->first();
        
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
        }

        $wishlist = Wishlist::firstOrCreate([
            'customer_id' => $customer->id,
            'product_id' => $request->product_id,
            'variation_id' => $request->variation_id,
        ]);

        if ($wishlist->wasRecentlyCreated) {
            return response()->json(['success' => true, 'message' => 'Added to wishlist!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Already in wishlist.']);
        }
    }

    public function destroy(Wishlist $wishlist)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();
        
        if ($customer && $wishlist->customer_id === $customer->id) {
            $wishlist->delete();
            return back()->with('success', 'Removed from wishlist!');
        }

        return back()->with('error', 'Unauthorized.');
    }
}
