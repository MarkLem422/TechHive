<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variations', 'primaryImage', 'productImages']);

        // Search by product name or variation name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('variations', function($q) use ($search) {
                      $q->where('variation_name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->whereHas('variations', function($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->whereHas('variations', function($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        // Filter by stock availability
        if ($request->has('stock_filter') && $request->stock_filter === 'in_stock') {
            $query->whereHas('variations', function($q) {
                $q->where('stock_quantity', '>', 0);
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('category_name')->get();

        return view('customer.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'variations.variantImages',
            'productImages',
            'reviews.customer',
        ]);
        
        // Get lowest price from variations
        $minPrice = $product->variations->min('price');
        $maxPrice = $product->variations->max('price');
        $totalStock = $product->variations->sum('stock_quantity');

        $relatedProducts = Product::with(['category', 'primaryImage', 'variations'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('customer.products.show', compact('product', 'minPrice', 'maxPrice', 'totalStock', 'relatedProducts'));
    }
}
