@extends('layouts.dashboard')

@php
    $header = 'Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
    $subtitle = 'Manage status, payment, and fulfillment details';
@endphp

@section('content')
    <div class="space-y-6">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-white/60">Items</p>
                            <h2 class="text-lg font-semibold text-white">Order Items</h2>
                        </div>
                        <a href="{{ route('seller.orders.index') }}" class="px-3 py-1.5 text-sm rounded-md border border-white/15 hover:bg-white/5 transition">Back to Orders</a>
                    </div>
                    <div class="space-y-4 divide-y divide-white/10">
                        @foreach($orderItems as $item)
                            <div class="py-4 flex justify-between items-start gap-4">
                                <div class="flex-1 space-y-1">
                                    <h3 class="font-semibold text-white">{{ $item['product']->product_name }}</h3>
                                    @if($item['variation'])
                                        <p class="text-sm text-white/70">Variation: {{ $item['variation']->variation_name }}</p>
                                        @if($item['variation']->stock_quantity <= 10)
                                            <p class="text-xs {{ $item['variation']->stock_quantity <= 0 ? 'text-red-300' : 'text-amber-200' }} font-semibold">
                                                ⚠ Stock: {{ $item['variation']->stock_quantity }} available
                                            </p>
                                        @endif
                                    @endif
                                    <p class="text-sm text-white/70">Quantity: {{ $item['quantity'] }}</p>
                                    <p class="text-sm text-white/70">Price: ₱{{ number_format($item['price'], 2) }} each</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-white">₱{{ number_format($item['total'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30">
                    <p class="text-xs uppercase tracking-wide text-white/60">Customer</p>
                    <h2 class="text-lg font-semibold text-white mb-3">Customer Information</h2>
                    <div class="space-y-2 text-sm text-white/80">
                        <div><span class="text-white/60">Name:</span> <span class="font-semibold">{{ $order->customer->first_name ?? 'N/A' }} {{ $order->customer->last_name ?? '' }}</span></div>
                        <div><span class="text-white/60">Email:</span> <span class="font-semibold">{{ $order->customer->email ?? $order->shipping_email }}</span></div>
                        <div><span class="text-white/60">Phone:</span> <span class="font-semibold">{{ $order->customer->phone ?? $order->shipping_phone }}</span></div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30">
                    <p class="text-xs uppercase tracking-wide text-white/60">Shipping</p>
                    <h2 class="text-lg font-semibold text-white mb-3">Shipping Information</h2>
                    <div class="space-y-2 text-sm text-white/80">
                        <div><span class="text-white/60">Name:</span> <span class="font-semibold">{{ $order->shipping_name }}</span></div>
                        <div><span class="text-white/60">Email:</span> <span class="font-semibold">{{ $order->shipping_email }}</span></div>
                        <div><span class="text-white/60">Phone:</span> <span class="font-semibold">{{ $order->shipping_phone }}</span></div>
                        <div><span class="text-white/60">Address:</span> <span class="font-semibold">{{ $order->shipping_address }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-[#0b1220] border border-white/15 rounded-2xl p-6 shadow-xl shadow-black/40 sticky top-6">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-white/60">Summary</p>
                            <h2 class="text-lg font-semibold text-white">Order Summary</h2>
                        </div>
                        <span class="text-sm text-white/60">{{ $order->order_date->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-white/80">
                            <span>Subtotal</span>
                            <span>₱{{ number_format($order->total_amount - $order->tax - $order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-white/70">
                            <span>Tax</span>
                            <span>₱{{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-white/70">
                            <span>Shipping</span>
                            <span>₱{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="border-t border-white/10 pt-3 mt-2">
                            <div class="flex justify-between text-base font-semibold">
                                <span>Total</span>
                                <span>₱{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-white/10 pt-4 mt-4 space-y-3 text-sm">
                        <div>
                            <p class="text-white/60 text-xs uppercase tracking-wide mb-1">Payment Method</p>
                            <div class="font-semibold text-white">{{ strtoupper($order->payment_method ?? 'N/A') }}</div>
                        </div>
                        <div>
                            <p class="text-white/60 text-xs uppercase tracking-wide mb-1">Payment Status</p>
                            @php
                                $payClass = match($order->payment_status) {
                                    'paid' => 'bg-emerald-500/15 text-emerald-100 border border-emerald-400/40',
                                    'pending' => 'bg-amber-500/15 text-amber-100 border border-amber-400/40',
                                    'failed' => 'bg-red-500/15 text-red-100 border border-red-400/40',
                                    'refunded' => 'bg-blue-500/15 text-blue-100 border border-blue-400/40',
                                    default => 'bg-white/10 text-white border border-white/15',
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold {{ $payClass }}">
                                {{ ucfirst($order->payment_status ?? 'N/A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Update Order Status -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-4">
                    <h2 class="text-lg font-semibold text-white">Update Order Status</h2>
                    
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                        <form action="{{ route('seller.orders.update-status', $order) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" onclick="return confirm('Mark this order as delivered?')" 
                                class="w-full px-4 py-2 bg-emerald-500 text-black font-semibold border border-emerald-400 rounded-md hover:bg-emerald-400 transition">
                                ✓ Complete Order (Mark as Delivered)
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('seller.orders.update-status', $order) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-amber-500 text-black font-semibold border border-amber-400 rounded-md hover:bg-amber-400 transition">
                            Update Status
                        </button>
                    </form>
                </div>

                <!-- Update Payment Status -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-3">
                    <h2 class="text-lg font-semibold text-white">Update Payment Status</h2>
                    <form action="{{ route('seller.orders.update-payment-status', $order) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Payment Status</label>
                            <select name="payment_status" class="w-full px-4 py-2.5 rounded-lg bg-[#0b1220] text-white border border-white/15 placeholder-white/50 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-amber-500 text-black font-semibold border border-amber-400 rounded-md hover:bg-amber-400 transition">
                            Update Payment Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

