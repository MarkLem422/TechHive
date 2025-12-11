@extends('layouts.dashboard', ['title' => 'Order Management - TechHive', 'header' => 'Order Management', 'subtitle' => 'Monitor and fulfill customer orders'])

@section('content')
    @if (session('success'))
        <div class="mt-4 mb-4 rounded-md border border-green-500/30 bg-green-500/10 text-green-100 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 mb-4 rounded-md border border-red-500/30 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Stock Notifications -->
    @if($outOfStockVariations->count() > 0)
        <div class="mb-4 rounded-md border border-red-500/30 bg-red-500/10 text-red-100 px-4 py-3">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <strong>Out of Stock Alert!</strong>
            </div>
            <p class="text-sm text-white/80">The following products are out of stock:</p>
            <ul class="text-sm mt-1 list-disc list-inside text-white/80">
                @foreach($outOfStockVariations->take(5) as $variation)
                    <li>{{ $variation->product->product_name }} - {{ $variation->variation_name }}</li>
                @endforeach
                @if($outOfStockVariations->count() > 5)
                    <li>... and {{ $outOfStockVariations->count() - 5 }} more</li>
                @endif
            </ul>
        </div>
    @endif

    @if($lowStockVariations->count() > 0)
        <div class="mb-4 rounded-md border border-orange-500/40 bg-orange-500/10 text-orange-100 px-4 py-3">
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
        </div>
    @endif

    <div class="space-y-4">
        <x-order-status-tabs :active="$activeStatus ?? 'all'" :route="'seller.orders.index'" />
        <div id="orders-list">
            @include('orders.partials.list', ['orders' => $orders])
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.order-tab');
        const listEl = document.getElementById('orders-list');

        const setActive = (clicked) => {
            tabs.forEach(t => t.classList.remove('font-semibold', 'text-white', 'border-b-2', 'border-orange-400'));
            clicked.classList.add('font-semibold', 'text-white', 'border-b-2', 'border-orange-400');
        };

        const fetchList = (url, fallback) => {
            const u = new URL(url, window.location.origin);
            u.searchParams.set('ajax', '1');
            fetch(u, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => { listEl.innerHTML = html; })
                .catch(() => { window.location = fallback; });
        };

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const url = tab.dataset.url;
                setActive(tab);
                fetchList(url, url);
            });
        });
    });
</script>
@endpush

