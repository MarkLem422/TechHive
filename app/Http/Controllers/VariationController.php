<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variation;
use App\Models\VariantImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VariationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function authorizeSeller(): ?RedirectResponse
    {
        if (Auth::user()?->role !== 'seller') {
            return redirect('/')->with('error', 'Access denied. Seller account required.');
        }

        return null;
    }

    public function store(Request $request, Product $product)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'variation_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'max:100', 'unique:variations,sku'],
            'variation_images' => ['array'],
            'variation_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $variation = $product->variations()->create($data);

        if ($request->hasFile('variation_images')) {
            foreach ($request->file('variation_images') as $file) {
                $path = $file->store('variant-images', 'public');
                $variation->variantImages()->create([
                    'product_variant_id' => $variation->id,
                    'image_path' => $path,
                ]);
            }
        }

        $this->syncProductPrice($product);

        return redirect()->route('products.show', $product)->with('success', 'Variation added.');
    }

    public function edit(Product $product, Variation $variation)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $variation->load(['product', 'variantImages']);

        return view('variations.edit', compact('product', 'variation'));
    }

    public function update(Request $request, Product $product, Variation $variation)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'variation_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'max:100', 'unique:variations,sku,' . $variation->id],
            'variation_images' => ['array'],
            'variation_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_variant_image' => ['array'],
            'remove_variant_image.*' => ['integer'],
        ]);

        $variation->update($data);

        if (!empty($data['remove_variant_image'])) {
            $imagesToRemove = $variation->variantImages()->whereIn('id', $data['remove_variant_image'])->get();
            foreach ($imagesToRemove as $image) {
                $this->deleteStoredImage($image->image_path);
                $image->delete();
            }
        }

        if ($request->hasFile('variation_images')) {
            foreach ($request->file('variation_images') as $file) {
                $path = $file->store('variant-images', 'public');
                $variation->variantImages()->create([
                    'product_variant_id' => $variation->id,
                    'image_path' => $path,
                ]);
            }
        }

        $this->syncProductPrice($product);

        return redirect()->route('products.show', $product)->with('success', 'Variation updated.');
    }

    public function destroy(Product $product, Variation $variation)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $variation->load('variantImages');
        foreach ($variation->variantImages as $image) {
            $this->deleteStoredImage($image->image_path);
        }

        $variation->delete();

        $this->syncProductPrice($product);

        return redirect()->route('products.show', $product)->with('success', 'Variation deleted.');
    }

    protected function deleteStoredImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function syncProductPrice(Product $product): void
    {
        $minPrice = $product->variations()->min('price');
        $product->update(['price' => $minPrice ?? 0]);
    }
}

