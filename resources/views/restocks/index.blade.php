@extends('layouts.dashboard')

@php
    $header = 'Restock History';
    $subtitle = 'Track every stock-in event across products and variants';
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-white/60">Inventory</p>
                <h2 class="text-2xl font-semibold text-white">Restock History</h2>
                <p class="text-sm text-white/60">View product and variant replenishments</p>
            </div>
            <a href="{{ route('restocks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-md border border-emerald-400/40 bg-emerald-500/20 text-emerald-50 hover:bg-emerald-400/30 transition">
                <span class="text-lg leading-none">+</span> Restock Product
            </a>
        </div>

        @if(isset($lowStockVariations) && $lowStockVariations->count() > 0)
            <div class="rounded-md border border-orange-500/40 bg-orange-500/10 text-orange-100 px-4 py-3">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <strong>Low Stock Warning!</strong>
                </div>
                <p class="text-sm text-white/80">The following products are running low on stock:</p>
                <ul class="text-sm mt-1 list-disc list-inside text-white/80">
                    @foreach($lowStockVariations->take(5) as $variation)
                        <li>{{ $variation->product->product_name }} - {{ $variation->variation_name }} ({{ $variation->stock_quantity }} remaining)</li>
                    @endforeach
                    @if($lowStockVariations->count() > 5)
                        <li>... and {{ $lowStockVariations->count() - 5 }} more</li>
                    @endif
                </ul>
                @if(isset($outOfStockVariations) && $outOfStockVariations->count() > 0)
                    <p class="text-sm text-red-200 mt-2">Out of stock: {{ $outOfStockVariations->count() }} variation(s). Consider restocking them soon.</p>
                @endif
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 text-emerald-100 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden border border-white/10 rounded-xl bg-white/5 backdrop-blur">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-white/60 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Product</th>
                        <th class="px-4 py-3 text-left font-semibold">Variation</th>
                        <th class="px-4 py-3 text-left font-semibold">Supplier</th>
                        <th class="px-4 py-3 text-left font-semibold">Qty Added</th>
                        <th class="px-4 py-3 text-left font-semibold">Prev Stock</th>
                        <th class="px-4 py-3 text-left font-semibold">New Stock</th>
                        <th class="px-4 py-3 text-left font-semibold">Cost/Unit</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($restocks as $restock)
                        <tr class="hover:bg-white/[0.04] transition">
                            <td class="px-4 py-3 text-sm text-white/80">
                                {{ $restock->restocked_at ? $restock->restocked_at->format('M d, Y H:i') : $restock->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-white">
                                {{ $restock->product->product_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/70">
                                {{ $restock->variation->variation_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/70">
                                {{ $restock->supplier->supplier_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white">
                                {{ number_format($restock->quantity_added) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white">
                                {{ number_format($restock->previous_stock ?? 0) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white">
                                <span class="font-semibold">{{ number_format($restock->new_stock ?? 0) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-white/70">
                                @if($restock->cost_per_unit)
                                    ₱{{ number_format($restock->cost_per_unit, 2) }}
                                @else
                                    <span class="text-white/50">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-white">
                                @if($restock->total_cost)
                                    <span class="font-semibold">₱{{ number_format($restock->total_cost, 2) }}</span>
                                @else
                                    <span class="text-white/50">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-white/60">No restock records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-white/70">
            {{ $restocks->links() }}
        </div>
    </div>
@endsection

