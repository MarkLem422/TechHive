@extends('layouts.dashboard', ['title' => 'Add Product - TechHive', 'header' => 'Add Product', 'subtitle' => 'Create a new product entry'])

@section('content')
    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-500/30 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-2">Product Name <span class="text-red-300">*</span></label>
            <input type="text" name="product_name" value="{{ old('product_name') }}" required
                class="w-full px-4 py-3 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Category <span class="text-red-300">*</span></label>
            <select name="category_id" required
                class="w-full px-4 py-3 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-3 rounded-sm bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">{{ old('description') }}</textarea>
        </div>

        <div class="border border-white/10 rounded-lg p-4 space-y-3 bg-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-white">Product Images</h3>
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
                    <option value="">Set after upload (defaults to first)</option>
                </select>
            </div>
            <div id="product-image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
        </div>

        <div class="border border-white/10 rounded-lg p-4 space-y-3 bg-white/5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Variations (required)</h3>
                <button type="button" id="add-variation" class="px-3 py-1.5 text-sm border border-white/20 hover:border-white/40 rounded-sm text-white bg-white/5">+ Add Variation</button>
            </div>
            <p class="text-xs text-white/70">Add at least one variation with price and stock.</p>
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

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-indigo-500 text-white border border-indigo-600 rounded-sm hover:bg-indigo-600 transition">
                Save Product
            </button>
            <a href="{{ route('products.index') }}" class="px-5 py-2 border border-white/20 hover:border-white/40 rounded-sm text-sm text-white">
                Cancel
            </a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
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
        addVariation(); // Start with one variation row

        const productImagesInput = document.getElementById('product_images');
        const primarySelect = document.getElementById('primary-image-select');
        const preview = document.getElementById('product-image-preview');

        const refreshPrimaryOptions = (files) => {
            if (!primarySelect) return;
            primarySelect.innerHTML = '<option value="">Default to first upload</option>';
            Array.from(files).forEach((file, idx) => {
                const opt = document.createElement('option');
                opt.value = `new-${idx}`;
                opt.textContent = `${idx + 1}. ${file.name}`;
                if (idx === 0) opt.selected = true;
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
                img.className = 'w-full h-32 object-cover rounded-sm border border-[#e3e3e0]';
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
    });
</script>
@endpush
