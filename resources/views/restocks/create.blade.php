@extends('layouts.dashboard')

@php
    $header = 'Restock Product';
    $subtitle = 'Add stock to a product or specific variant';
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

        <div class="bg-white/5 border border-white/10 rounded-2xl shadow-lg shadow-black/30 p-6 space-y-6">
            <div>
                <p class="text-xs uppercase tracking-wide text-white/60">Inventory</p>
                <h2 class="text-2xl font-semibold text-white">Restock Product</h2>
                <p class="text-sm text-white/60">Add units, costs, and notes for traceability.</p>
            </div>

            <form action="{{ route('restocks.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-white mb-2">Product <span class="text-red-300">*</span></label>
                    <select name="product_id" id="product_id" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                        <option value="" disabled {{ (old('product_id') || $selectedProductId) ? '' : 'selected' }}>Select a product</option>
                        @foreach($products as $product)
                            @php
                                $currentStock = $product->variations->sum('stock_quantity');
                                $isSelected = old('product_id') == $product->id || (isset($selectedProductId) && $selectedProductId == $product->id);
                            @endphp
                            <option value="{{ $product->id }}" data-variations="{{ json_encode($product->variations) }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $product->product_name }} (Current Stock: {{ $currentStock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">Variation <span class="text-white/50 text-xs">(optional - defaults to first variation)</span></label>
                    <select name="variation_id" id="variation_id"
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                        <option value="">Select a variation (optional)</option>
                    </select>
                    <p class="mt-1 text-xs text-white/60">If no variation is selected, stock will be added to the first/default variation.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">Supplier <span class="text-red-300">*</span></label>
                    <select name="supplier_id" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                        <option value="" disabled {{ old('supplier_id') ? '' : 'selected' }}>Select a supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Quantity Added <span class="text-red-300">*</span></label>
                        <input type="number" min="1" name="quantity_added" value="{{ old('quantity_added', 1) }}" required
                            class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Cost Per Unit (₱) <span class="text-white/50 text-xs">(optional)</span></label>
                        <input type="number" step="0.01" min="0" name="cost_per_unit" value="{{ old('cost_per_unit') }}"
                            class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none"
                            placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">Note <span class="text-white/50 text-xs">(optional)</span></label>
                    <textarea name="note" rows="3"
                        class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">{{ old('note') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 text-black font-semibold border border-amber-400 rounded-md hover:bg-amber-400 transition">
                        Restock Product
                    </button>
                    <a href="{{ route('restocks.index') }}" class="px-5 py-2.5 text-sm rounded-md border border-white/15 hover:bg-white/5 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const productSelect = document.getElementById('product_id');
            const variationSelect = document.getElementById('variation_id');
            const selectedVariationId = @json(old('variation_id', $selectedVariationId ?? null));

            function updateVariations() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const variations = selectedOption ? JSON.parse(selectedOption.getAttribute('data-variations') || '[]') : [];

                variationSelect.innerHTML = '<option value=\"\">Select a variation (optional)</option>';

                variations.forEach(variation => {
                    const option = document.createElement('option');
                    option.value = variation.id;
                    option.textContent = `${variation.variation_name} (Stock: ${variation.stock_quantity})`;
                    if (selectedVariationId && Number(selectedVariationId) === Number(variation.id)) {
                        option.selected = true;
                    }
                    variationSelect.appendChild(option);
                });
            }

            productSelect.addEventListener('change', updateVariations);

            if (productSelect.value) {
                updateVariations();
            }
        })();
    </script>
@endpush

