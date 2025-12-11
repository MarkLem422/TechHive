<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function authorizeSeller()
    {
        if (Auth::user()?->role !== 'seller') {
            return redirect('/')->with('error', 'Access denied. Seller account required.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $restocks = Restock::with(['product', 'supplier', 'variation'])
            ->whereNotNull('product_id')
            ->orderByDesc('restocked_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        $lowStockVariations = Variation::where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->with('product')
            ->get();

        $outOfStockVariations = Variation::where('stock_quantity', '<=', 0)
            ->with('product')
            ->get();

        return view('restocks.index', compact('restocks', 'lowStockVariations', 'outOfStockVariations'));
    }

    public function create(Request $request)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $products = Product::with('variations')->orderBy('product_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $selectedProductId = $request->query('product_id');
        $selectedVariationId = $request->query('variation_id');

        return view('restocks.create', compact('products', 'suppliers', 'selectedProductId', 'selectedVariationId'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variation_id' => ['nullable', 'exists:variations,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quantity_added' => ['required', 'integer', 'min:1'],
            'cost_per_unit' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::with('variations')->findOrFail($data['product_id']);
        
        // Get the variation to restock
        if (!empty($data['variation_id'])) {
            $variation = Variation::where('id', $data['variation_id'])
                ->where('product_id', $product->id)
                ->firstOrFail();
        } else {
            // Use first/default variation if none specified
            $variation = $product->variations->first();
            if (!$variation) {
                $variation = $product->variations()->create([
                    'variation_name' => 'Default',
                    'price' => $product->price,
                    'stock_quantity' => 0,
                    'sku' => 'DEFAULT-' . $product->id,
                ]);
            }
        }
        
        // Get current stock for this variation
        $previousStock = $variation->stock_quantity;
        
        // Calculate new stock for this variation
        $newStock = $previousStock + $data['quantity_added'];
        
        // Calculate total cost
        $totalCost = $data['cost_per_unit'] ? ($data['quantity_added'] * $data['cost_per_unit']) : null;

        // Add stock to the variation
        $variation->increment('stock_quantity', $data['quantity_added']);

        // Create restock record
        Restock::create([
            'product_id' => $product->id,
            'variation_id' => $variation->id,
            'supplier_id' => $data['supplier_id'],
            'quantity_added' => $data['quantity_added'],
            'cost_per_unit' => $data['cost_per_unit'],
            'total_cost' => $totalCost,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'restocked_at' => now(),
            'note' => $data['note'] ?? null,
        ]);

        return redirect()
            ->route('restocks.index')
            ->with('success', 'Product restocked successfully.');
    }

    // Keep existing method for variation-level restocking (backward compatibility)
    public function storeVariation(Request $request, Variation $variation)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $previousStock = $variation->stock_quantity;
        $newStock = $previousStock + $data['quantity'];

        Restock::create([
            'product_id' => $variation->product_id,
            'variation_id' => $variation->id,
            'supplier_id' => $data['supplier_id'],
            'quantity_added' => $data['quantity'],
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'restocked_at' => now(),
            'note' => $data['note'] ?? null,
        ]);

        $variation->increment('stock_quantity', $data['quantity']);

        return redirect()->route('products.show', $variation->product_id)->with('success', 'Stock updated.');
    }
}

