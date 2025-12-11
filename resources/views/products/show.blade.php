@extends('layouts.dashboard')

@php
    use Illuminate\Support\Facades\Storage;
    $header = 'Product Details';
    $subtitle = 'View and manage this product';
    $stockQty = $product->variations->sum('stock_quantity');
    $primaryImage = $product->productImages->firstWhere('is_primary', true) ?? $product->productImages->first();
    $minPrice = $product->variations->min('price');
@endphp

@section('content')
    <div class="space-y-6">
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 shadow-lg shadow-black/20">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wide bg-amber-500/20 text-amber-200 rounded-full">Product</span>
                        <span class="text-white/60 text-sm">ID: {{ $product->id }}</span>
                    </div>
                    <h2 class="text-2xl font-semibold mt-2">{{ $product->product_name }}</h2>
                    <p class="text-sm text-white/60">
                        Category: {{ $product->category->category_name ?? 'Uncategorized' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 text-sm rounded-md border border-white/15 bg-white/5 hover:bg-white/10 transition">Edit</a>
                    <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm rounded-md border border-white/15 hover:bg-white/5 transition">Back to list</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6">
                <div class="md:col-span-2 space-y-3">
                    <div class="rounded-xl border border-white/10 bg-white/5 overflow-hidden min-h-[280px] flex items-center justify-center">
                        @if($primaryImage)
                            <img src="{{ Storage::url($primaryImage->image_path) }}" class="w-full h-72 object-cover" alt="Primary product image">
                        @else
                            <div class="text-white/60 text-sm py-12">No images uploaded yet</div>
                        @endif
                    </div>
                    @if($product->productImages->count() > 1)
                        <div class="grid grid-cols-4 gap-3">
                            @foreach($product->productImages as $image)
                                <img src="{{ Storage::url($image->image_path) }}"
                                     class="w-full h-20 object-cover rounded-lg border {{ $image->id === optional($primaryImage)->id ? 'border-amber-400' : 'border-white/10' }}"
                                     alt="Product image thumbnail">
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="space-y-4 text-sm">
                    <div class="p-4 rounded-xl border border-white/10 bg-white/5">
                        <p class="text-white/60 text-xs uppercase tracking-wide mb-1">Price</p>
                        <p class="text-xl font-semibold">
                            @if($minPrice !== null)
                                From ₱{{ number_format($minPrice, 2) }}
                            @else
                                ₱{{ number_format($product->price, 2) }}
                            @endif
                        </p>
                        <p class="text-white/60 text-xs mt-1">Stock: {{ $stockQty }}</p>
                    </div>
                    <div class="p-4 rounded-xl border border-white/10 bg-white/5 space-y-2">
                        <p class="text-sm"><span class="font-semibold text-white">Images:</span> {{ $product->productImages->count() }}</p>
                        <p class="text-sm"><span class="font-semibold text-white">Variations:</span> {{ $product->variations->count() }}</p>
                        <p class="text-xs text-white/60">Manage images in the edit page.</p>
                    </div>
                    @if ($product->description)
                        <div class="p-4 rounded-xl border border-white/10 bg-white/5">
                            <h3 class="text-sm font-semibold mb-1">Description</h3>
                            <p class="text-sm text-white/80 leading-relaxed">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-xl p-6 shadow-lg shadow-black/20 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Variations</h3>
                    <p class="text-sm text-white/60">Manage pricing, stock, and images per variant.</p>
                </div>
                <span class="text-sm text-white/60">{{ $product->variations->count() }} items</span>
            </div>

            <form action="{{ route('variations.store', $product) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end bg-white/5 border border-white/10 rounded-lg p-4">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Variation Name</label>
                    <input type="text" name="variation_name" required
                        class="w-full px-3 py-2 rounded-md bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Price (₱)</label>
                    <input type="number" step="0.01" min="0" name="price" required
                        class="w-full px-3 py-2 rounded-md bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Stock Qty</label>
                    <input type="number" min="0" name="stock_quantity" required
                        class="w-full px-3 py-2 rounded-md bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">SKU</label>
                    <input type="text" name="sku" required
                        class="w-full px-3 py-2 rounded-md bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium mb-1">Variation Images</label>
                    <input type="file" name="variation_images[]" accept=".jpg,.jpeg,.png,.webp" multiple
                        class="w-full px-3 py-2 rounded-md bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    <p class="text-xs text-white/50 mt-1">Optional: add images specific to this variation.</p>
                </div>
                <div class="md:col-span-5 flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-amber-500 text-black font-semibold border border-amber-400 rounded-md hover:bg-amber-400 transition">Add Variation</button>
                </div>
            </form>

            @if ($product->variations->isEmpty())
                <p class="text-sm text-white/60">No variations yet.</p>
            @else
                <div class="overflow-hidden border border-white/10 rounded-xl">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Images</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-white/70 uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($product->variations as $variation)
                                <tr class="bg-white/[0.02] hover:bg-white/[0.04]">
                                    <td class="px-4 py-3 text-sm font-medium text-white">{{ $variation->variation_name }}</td>
                                    <td class="px-4 py-3 text-sm text-white/90">₱{{ number_format($variation->price, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-white/90">{{ $variation->stock_quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-white/60">{{ $variation->sku }}</td>
                                    <td class="px-4 py-3 text-sm text-white/70">
                                        {{ $variation->variantImages->count() }} images
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                        <div class="flex items-center gap-2 justify-end">
                                            <a href="{{ route('variations.edit', [$product, $variation]) }}" class="px-3 py-1.5 text-sm rounded-md border border-white/15 bg-white/5 hover:bg-white/10 whitespace-nowrap">Edit</a>
                                            <form action="{{ route('variations.destroy', [$product, $variation]) }}" method="POST" onsubmit="return confirm('Delete this variation?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-sm rounded-md border border-red-400/50 text-red-300 hover:bg-red-500/10 whitespace-nowrap">Delete</button>
                                            </form>
                                            <a href="{{ route('restocks.create', ['product_id' => $product->id, 'variation_id' => $variation->id]) }}" class="px-3 py-1.5 text-sm rounded-md border border-amber-300/50 bg-amber-500/10 text-amber-200 hover:bg-amber-400/20 whitespace-nowrap">Restock</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

