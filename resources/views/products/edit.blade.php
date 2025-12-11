@extends('layouts.dashboard', ['title' => 'Edit Product - TechHive', 'header' => 'Edit Product', 'subtitle' => 'Update product details'])

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;
        $currentPrimaryId = $product->productImages->firstWhere('is_primary', true)?->id ?? null;
    @endphp

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-500/30 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-2">Product Name <span class="text-red-300">*</span></label>
            <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required
                class="w-full px-4 py-3 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Category <span class="text-red-300">*</span></label>
            <select name="category_id" required
                class="w-full px-4 py-3 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-3 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-indigo-500 text-white border border-indigo-600 rounded-sm hover:bg-indigo-600 transition">
                Update Product
            </button>
            <a href="{{ route('products.index') }}" class="px-5 py-2 border border-white/20 hover:border-white/40 rounded-sm text-sm text-white">
                Cancel
            </a>
        </div>

        <div class="border border-white/10 rounded-lg p-4 space-y-4 bg-white/5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Current Images</h3>
                <span class="text-xs text-white/70">Set one as primary and remove old ones.</span>
            </div>
            @if($product->productImages->isEmpty())
                <p class="text-sm text-white/70">No images yet. Upload new images below.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($product->productImages as $image)
                        <div class="border border-white/10 rounded-lg overflow-hidden bg-white/5">
                            <img src="{{ Storage::url($image->image_path) }}" alt="Product image" class="w-full h-36 object-cover">
                            <div class="flex items-center justify-between px-3 py-2 text-xs text-white">
                                <label class="inline-flex items-center gap-1">
                                    <input type="radio" name="primary_image" value="existing-{{ $image->id }}" {{ $image->id === $currentPrimaryId ? 'checked' : '' }}>
                                    <span>Primary</span>
                                </label>
                                <label class="inline-flex items-center gap-1 text-red-200">
                                    <input type="checkbox" name="remove_product_image[]" value="{{ $image->id }}">
                                    <span>Remove</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="border border-white/10 rounded-lg p-4 space-y-3 bg-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-white">Add New Images</h3>
                    <p class="text-xs text-white/70">Upload JPG, PNG, or WEBP. Max 5MB each.</p>
                </div>
                <label class="text-xs text-white border border-white/20 px-3 py-1.5 rounded-sm cursor-pointer bg-white/5 hover:border-white/40">
                    <input type="file" name="product_images[]" id="product_images" class="hidden" accept=".jpg,.jpeg,.png,.webp" multiple>
                    Select files
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Primary Image</label>
                <select id="primary-image-select" name="primary_image" class="w-full px-3 py-2 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    <option value="">Keep current primary or choose new upload</option>
                    @foreach($product->productImages as $image)
                        <option value="existing-{{ $image->id }}" {{ $image->id === $currentPrimaryId ? 'selected' : '' }}>
                            Existing: #{{ $image->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="product-image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
        </div>

        <div class="border border-white/10 rounded-lg p-4 space-y-3 bg-white/5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Add New Variations (required: keep at least one)</h3>
                <button type="button" id="add-variation" class="px-3 py-1.5 text-sm border border-white/20 hover:border-white/40 rounded-sm text-white bg-white/5">+ Add Variation</button>
            </div>
            <p class="text-xs text-white/70">Add one or more variations with price and stock.</p>
            <div id="variation-list" class="space-y-3"></div>
            <template id="variation-template">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 border border-dashed border-white/20 rounded-sm p-3 bg-white/5" data-variation-row>
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" name="variation_name[]" class="w-full px-3 py-2 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Price</label>
                        <input type="number" step="0.01" min="0" name="variation_price[]" class="w-full px-3 py-2 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Stock</label>
                        <input type="number" min="0" name="variation_stock[]" class="w-full px-3 py-2 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">SKU</label>
                        <input type="text" name="variation_sku[]" class="w-full px-3 py-2 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium mb-1">Variation Images</label>
                        <input type="file" data-variation-image-input accept=".jpg,.jpeg,.png,.webp" multiple class="w-full px-3 py-2 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                        <p class="text-xs text-white/70 mt-1">Optional images specific to this variant.</p>
                    </div>
                </div>
            </template>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (() => {
        const addBtn = document.getElementById('add-variation');
        const list = document.getElementById('variation-list');
        const tpl = document.getElementById('variation-template');
        let variationIndex = 0;

        const addVariation = () => {
            if (!tpl || !list) return;
            const fragment = tpl.content.cloneNode(true);
            const imageInput = fragment.querySelector('[data-variation-image-input]');
            if (imageInput) {
                imageInput.name = `variation_images[${variationIndex}][]`;
            }
            list.appendChild(fragment);
            variationIndex++;
        };

        if (addBtn) {
            addBtn.addEventListener('click', addVariation);
        }
        addVariation(); // Provide one empty row for quick add

        const productImagesInput = document.getElementById('product_images');
        const primarySelect = document.getElementById('primary-image-select');
        const preview = document.getElementById('product-image-preview');

        const refreshPrimaryOptions = (files) => {
            if (!primarySelect) return;
            const preservedOptions = Array.from(primarySelect.querySelectorAll('option')).filter(opt => opt.value.startsWith('existing-'));
            primarySelect.innerHTML = '<option value="">Keep current primary or choose new upload</option>';
            preservedOptions.forEach(opt => primarySelect.appendChild(opt));
            Array.from(files).forEach((file, idx) => {
                const opt = document.createElement('option');
                opt.value = `new-${idx}`;
                opt.textContent = `New: ${file.name}`;
                primarySelect.appendChild(opt);
            });
        };

        const renderPreview = (files) => {
            if (!preview) return;
            preview.innerHTML = '';
            Array.from(files).forEach((file) => {
                const url = URL.createObjectURL(file);
                const img = document.createElement('img');
                img.src = url;
                img.className = 'w-full h-32 object-cover rounded-sm border border-white/20';
                preview.appendChild(img);
            });
        };

        if (productImagesInput) {
            productImagesInput.addEventListener('change', (e) => {
                const files = e.target.files || [];
                refreshPrimaryOptions(files);
                renderPreview(files);
            });
        }
    })();
</script>
@endpush

