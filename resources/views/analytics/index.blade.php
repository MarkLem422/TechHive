@extends('layouts.dashboard')

@php
    $header = 'Analytics & Reports';
    $subtitle = 'Track revenue, order flow, and best performing products';
    $hasDateFilters = request()->filled('start_date') || request()->filled('end_date');
@endphp

@section('content')
    <div class="space-y-6">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30">
            <div class="flex flex-col gap-2 mb-4">
                <p class="text-xs uppercase tracking-wide text-white/60">Filters</p>
                <h2 class="text-xl font-semibold text-white">Date range</h2>
            </div>
            <form method="GET" action="{{ route('analytics.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm">
                <div>
                    <label class="block text-white/80 mb-1">Start Date (optional)</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/15 text-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    <p class="text-xs text-white/50 mt-1">Leave blank for all time</p>
                </div>
                <div>
                    <label class="block text-white/80 mb-1">End Date (optional)</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/15 text-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    <p class="text-xs text-white/50 mt-1">Defaults to today</p>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-3 py-2 bg-amber-500 text-black font-semibold rounded-lg border border-amber-400 hover:bg-amber-400 transition">
                        Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('analytics.index') }}" class="w-full text-center px-3 py-2 rounded-lg border border-white/15 text-white hover:bg-white/5 transition">
                        Reset (All Time)
                    </a>
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="setLast30Days()" class="w-full px-3 py-2 rounded-lg border border-sky-400/40 text-sky-200 hover:bg-sky-500/10 transition">
                        Last 30 Days
                    </button>
                </div>
            </form>
        </div>

        @if ($totalOrders == 0)
            <div class="bg-amber-500/10 border border-amber-400/40 rounded-2xl p-5 text-amber-100">
                <div class="flex items-center gap-2 font-semibold text-amber-200 mb-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    No sales data found
                </div>
                <p class="text-sm">
                    @if ($hasDateFilters)
                        No orders were found in the selected window. Try adjusting the range or reset to view all-time performance.
                    @else
                        Orders will appear here once customers start purchasing. Keep an eye on this dashboard for real-time insights.
                    @endif
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-white/50 mb-2">Total Sales (Delivered)</p>
                <p class="text-3xl font-semibold text-white">₱{{ number_format($totalSales, 2) }}</p>
                <p class="text-xs text-white/60 mt-1">Completed orders only</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-white/50 mb-2">Total Orders</p>
                <p class="text-3xl font-semibold text-white">{{ number_format($totalOrders) }}</p>
                <p class="text-xs text-white/60 mt-1">Excludes cancelled</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-white/50 mb-2">Completed Orders</p>
                <p class="text-3xl font-semibold text-white">{{ number_format($completedOrders) }}</p>
                <p class="text-xs text-white/60 mt-1">Delivered status</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-white/50 mb-2">Average Order Value</p>
                <p class="text-3xl font-semibold text-white">₱{{ $completedOrders > 0 ? number_format($totalSales / $completedOrders, 2) : '0.00' }}</p>
                <p class="text-xs text-white/60 mt-1">Delivered orders</p>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-white/60">Status Overview</p>
                    <h3 class="text-lg font-semibold text-white">Orders by status</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm divide-y divide-white/10">
                    <thead class="text-white/60 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-2">Status</th>
                            <th class="text-left py-2">Count</th>
                            <th class="text-right py-2">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($ordersByStatus as $status)
                            <tr class="text-white/80">
                                <td class="py-2">{{ ucfirst($status->status) }}</td>
                                <td class="py-2">{{ $status->count }}</td>
                                <td class="py-2 text-right">₱{{ number_format($status->revenue ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-white/60">Performance</p>
                    <h3 class="text-lg font-semibold text-white">Best selling products</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm divide-y divide-white/10">
                    <thead class="text-white/60 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-2">Product</th>
                            <th class="text-right py-2">Quantity</th>
                            <th class="text-right py-2">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($bestSellingProducts as $product)
                            <tr class="text-white/80">
                                <td class="py-2">{{ $product->product_name }}</td>
                                <td class="py-2 text-right">{{ $product->total_quantity }}</td>
                                <td class="py-2 text-right">₱{{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-white/60">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-white/60">Reports</p>
                <h3 class="text-lg font-semibold text-white">Generate CSV exports</h3>
            </div>
            <form action="{{ route('analytics.generate-report') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                @csrf
                <div>
                    <label class="block text-white/80 mb-1">Report Type</label>
                    <select name="report_type" required class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/15 text-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                        <option value="products">Products Report</option>
                        <option value="suppliers">Suppliers Report</option>
                        <option value="orders">Orders Report</option>
                    </select>
                </div>
                <div>
                    <label class="block text-white/80 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" required
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/15 text-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-white/80 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" required
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/15 text-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-3 py-2 bg-emerald-500 text-black font-semibold rounded-lg border border-emerald-300 hover:bg-emerald-400 transition">
                        Download CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setLast30Days() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 30);

            document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
            document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
        }
    </script>
@endpush

