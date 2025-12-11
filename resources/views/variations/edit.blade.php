@extends('layouts.dashboard')

@php
    use Illuminate\Support\Facades\Storage;
    $header = 'Edit Variation';
    $subtitle = 'Update variant details, pricing, stock, and images';
@endphp

@section('content')
    <div class="space-y-6 max-w-4xl">
        @if ($errors->any())
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('variations.update', [$product, $variation]) }}" method="POST" enctype="multipart/form-data" class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-white mb-2">Variation Name <span class="text-red-300">*</span></label>
                    <input type="text" name="variation_name" value="{{ old('variation_name', $variation->variation_name) }}" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Price (₱) <span class="text-red-300">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $variation->price) }}" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Stock Quantity <span class="text-red-300">*</span></label>
                    <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $variation->stock_quantity) }}" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white mb-2">SKU <span class="text-red-300">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku', $variation->sku) }}" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white">Variation Images</h3>
                    <span class="text-xs text-white/60">JPG, PNG, WEBP up to 5MB.</span>
                </div>
                @if($variation->variantImages->isEmpty())
                    <p class="text-sm text-white/70">No images yet for this variation.</p>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($variation->variantImages as $image)
                            <label class="block border border-white/10 rounded-lg overflow-hidden bg-white/5">
                                <img src="{{ Storage::url($image->image_path) }}" alt="Variant image" class="w-full h-28 object-cover">
                                <div class="px-3 py-2 flex items-center gap-2 text-xs text-red-200">
                                    <input type="checkbox" name="remove_variant_image[]" value="{{ $image->id }}">
                                    <span>Remove</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium mb-1 text-white">Upload New Images</label>
                    <input type="file" name="variation_images[]" accept=".jpg,.jpeg,.png,.webp" multiple
                        class="w-full px-3 py-2 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-amber-500 text-black font-semibold border border-amber-400 rounded-md hover:bg-amber-400 transition">
                    Update Variation
                </button>
                <a href="{{ route('products.show', $product) }}" class="px-5 py-2 border border-white/15 hover:bg-white/5 rounded-md text-sm text-white transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

