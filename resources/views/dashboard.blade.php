@extends('layouts.dashboard', ['title' => 'Dashboard - ' . config('app.name', 'Laravel'), 'header' => 'Dashboard'])

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
            <div class="text-sm text-white/70 mb-2">Total Products</div>
            <div class="text-2xl font-semibold text-white">{{ number_format($stats['total_products']) }}</div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
            <div class="text-sm text-white/70 mb-2">Total Categories</div>
            <div class="text-2xl font-semibold text-white">{{ number_format($stats['total_categories']) }}</div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
            <div class="text-sm text-white/70 mb-2">Total Orders</div>
            <div class="text-2xl font-semibold text-white">{{ number_format($stats['total_orders']) }}</div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
            <div class="text-sm text-white/70 mb-2">Total Customers</div>
            <div class="text-2xl font-semibold text-white">{{ number_format($stats['total_customers']) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
            <div class="text-sm text-white/70 mb-2">Total Revenue</div>
            <div class="text-xl font-semibold text-white">${{ number_format($stats['total_revenue'], 2) }}</div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
            <div class="text-sm text-white/70 mb-2">Pending Orders</div>
            <div class="text-xl font-semibold text-white">{{ number_format($stats['pending_orders']) }}</div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
            <div class="text-sm text-white/70 mb-2">Low Stock Products</div>
            <div class="text-xl font-semibold text-white">{{ number_format($stats['low_stock_products']) }}</div>
        </div>
    </div>

    <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Recent Orders</h2>
        @if($recent_orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-white/70">
                            <th class="text-left py-2">Order ID</th>
                            <th class="text-left py-2">Customer</th>
                            <th class="text-left py-2">Date</th>
                            <th class="text-left py-2">Amount</th>
                            <th class="text-left py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_orders as $order)
                            <tr class="border-b border-white/5">
                                <td class="py-2 text-white">#{{ $order->id }}</td>
                                <td class="py-2 text-white/80">
                                    @if($order->customer)
                                        {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="py-2 text-white/80">{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</td>
                                <td class="py-2 text-white">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-1 rounded-sm text-xs border border-white/20 text-white/90">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-white/70">No orders found.</p>
        @endif
    </div>
@endsection
