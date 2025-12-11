<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Variation;
use App\Models\ProductImage;
use App\Models\VariantImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function authorizeSeller(): ?RedirectResponse
    {
        if (Auth::user()?->role !== 'seller') {
            return redirect('/')
                ->with('error', 'Access denied. Seller account required.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $products = Product::with(['category', 'variations', 'primaryImage'])->orderByDesc('created_at')->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $categories = Category::orderBy('category_name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'variation_name' => ['array', 'min:1'],
            'variation_name.*' => ['nullable', 'string', 'max:255'],
            'variation_price' => ['array', 'min:1'],
            'variation_price.*' => ['nullable', 'numeric', 'min:0'],
            'variation_stock' => ['array', 'min:1'],
            'variation_stock.*' => ['nullable', 'integer', 'min:0'],
            'variation_sku' => ['array'],
            'variation_sku.*' => ['nullable', 'string', 'max:100'],
            'product_images' => ['array'],
            'product_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'primary_image' => ['nullable', 'string'],
            'variation_images' => ['array'],
            'variation_images.*' => ['array'],
            'variation_images.*.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $variations = [];
        if (!empty($data['variation_name'])) {
            $count = count($data['variation_name']);
            for ($i = 0; $i < $count; $i++) {
                $name = $data['variation_name'][$i] ?? null;
                $price = $data['variation_price'][$i] ?? null;
                $stock = $data['variation_stock'][$i] ?? null;
                $sku = $data['variation_sku'][$i] ?? null;
                if ($name && $price !== null && $stock !== null) {
                    $variations[] = [
                        'variation_name' => $name,
                        'price' => $price,
                        'stock_quantity' => $stock,
                        'sku' => $sku ?: uniqid('SKU-'),
                    ];
                }
            }
        }

        if (empty($variations)) {
            return back()->withErrors(['variation_name' => 'Add at least one variation with name, price, and stock.'])->withInput();
        }

        $minPrice = collect($variations)->min('price');

        $product = Product::create([
            'product_name' => $data['product_name'],
            'description' => $data['description'] ?? null,
            'price' => $minPrice,
            'category_id' => $data['category_id'],
        ]);

        $createdVariations = [];
        foreach ($variations as $index => $variation) {
            $createdVariations[$index] = $product->variations()->create($variation);
        }

        // Save product images
        $primarySelection = $data['primary_image'] ?? null;
        $primaryImageId = null;
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $index => $file) {
                $path = $file->store('product-images', 'public');
                $image = $product->productImages()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                ]);

                if ($primarySelection === 'new-' . $index) {
                    $primaryImageId = $image->id;
                }
                if ($primarySelection === null && $index === 0) {
                    $primaryImageId = $image->id;
                }
            }
        }

        if ($primaryImageId) {
            $product->productImages()->update(['is_primary' => false]);
            ProductImage::where('id', $primaryImageId)->update(['is_primary' => true]);
        }

        // Save variant images mapped by variation index
        if (!empty($data['variation_images'])) {
            foreach ($createdVariations as $index => $variation) {
                $files = $request->file("variation_images.$index") ?? [];
                foreach ($files as $file) {
                    $path = $file->store('variant-images', 'public');
                    $variation->variantImages()->create([
                        'product_variant_id' => $variation->id,
                        'image_path' => $path,
                    ]);
                }
            }
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $product->load(['category', 'variations.variantImages', 'productImages']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $categories = Category::orderBy('category_name')->get();
        $product->load(['variations', 'productImages']);

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'variation_name' => ['array'],
            'variation_name.*' => ['nullable', 'string', 'max:255'],
            'variation_price' => ['array'],
            'variation_price.*' => ['nullable', 'numeric', 'min:0'],
            'variation_stock' => ['array'],
            'variation_stock.*' => ['nullable', 'integer', 'min:0'],
            'variation_sku' => ['array'],
            'variation_sku.*' => ['nullable', 'string', 'max:100'],
            'product_images' => ['array'],
            'product_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'primary_image' => ['nullable', 'string'],
            'remove_product_image' => ['array'],
            'remove_product_image.*' => ['integer'],
            'variation_images' => ['array'],
            'variation_images.*' => ['array'],
            'variation_images.*.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Add any new variations
        $createdVariations = [];
        if (!empty($data['variation_name'])) {
            $count = count($data['variation_name']);
            for ($i = 0; $i < $count; $i++) {
                $name = $data['variation_name'][$i] ?? null;
                $price = $data['variation_price'][$i] ?? null;
                $stock = $data['variation_stock'][$i] ?? null;
                $sku = $data['variation_sku'][$i] ?? null;
                if ($name && $price !== null && $stock !== null) {
                    $createdVariations[$i] = $product->variations()->create([
                        'variation_name' => $name,
                        'price' => $price,
                        'stock_quantity' => $stock,
                        'sku' => $sku ?: uniqid('SKU-'),
                    ]);
                }
            }
        }

        // Ensure at least one variation exists
        if ($product->variations()->count() === 0) {
            return back()->withErrors(['variation_name' => 'Add at least one variation with name, price, and stock.'])->withInput();
        }

        // Update product data and derive price from min variation price
        $minPrice = $product->variations()->min('price');
        $product->update([
            'product_name' => $data['product_name'],
            'description' => $data['description'] ?? null,
            'price' => $minPrice,
            'category_id' => $data['category_id'],
        ]);

        // Remove selected product images
        if (!empty($data['remove_product_image'])) {
            $imagesToRemove = $product->productImages()->whereIn('id', $data['remove_product_image'])->get();
            foreach ($imagesToRemove as $image) {
                $this->deleteStoredImage($image->image_path);
                $image->delete();
            }
        }

        // Upload new product images
        $primarySelection = $data['primary_image'] ?? null;
        $primaryImageId = null;
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $index => $file) {
                $path = $file->store('product-images', 'public');
                $image = $product->productImages()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                ]);

                if ($primarySelection === 'new-' . $index) {
                    $primaryImageId = $image->id;
                }
                if ($primarySelection === null && $primaryImageId === null) {
                    $primaryImageId = $image->id;
                }
            }
        }

        // Re-run primary selection if existing image chosen
        if ($primarySelection && str_starts_with($primarySelection, 'existing-')) {
            $selectedId = (int) str_replace('existing-', '', $primarySelection);
            if ($product->productImages()->where('id', $selectedId)->exists()) {
                $primaryImageId = $selectedId;
            }
        }

        if ($primaryImageId) {
            $product->productImages()->update(['is_primary' => false]);
            ProductImage::where('id', $primaryImageId)->update(['is_primary' => true]);
        } elseif ($product->productImages()->exists()) {
            // Ensure at least one primary image
            $firstId = $product->productImages()->value('id');
            ProductImage::where('id', $firstId)->update(['is_primary' => true]);
        }

        // Store images for any newly added variations
        if (!empty($data['variation_images'])) {
            foreach ($createdVariations as $index => $variation) {
                $files = $request->file("variation_images.$index") ?? [];
                foreach ($files as $file) {
                    $path = $file->store('variant-images', 'public');
                    $variation->variantImages()->create([
                        'product_variant_id' => $variation->id,
                        'image_path' => $path,
                    ]);
                }
            }
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        // Delete product and variant images from storage
        $product->load(['productImages', 'variations.variantImages']);
        foreach ($product->productImages as $image) {
            $this->deleteStoredImage($image->image_path);
        }
        foreach ($product->variations as $variation) {
            foreach ($variation->variantImages as $image) {
                $this->deleteStoredImage($image->image_path);
            }
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    protected function deleteStoredImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
