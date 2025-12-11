<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order #{{ $order->id }} - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#0b1220] via-[#0b1220] to-[#0f172a] text-white">
    <header class="w-full border-b border-white/5 bg-[#0b1220]/70 backdrop-blur">
        <div class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-white/50">Order</p>
                <h1 class="text-2xl font-semibold">Order #{{ $order->id }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('orders.index') }}" class="px-4 py-2 text-sm rounded-md border border-white/15 hover:bg-white/5 transition">Back to Orders</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8 space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 text-emerald-100 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left column -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                            <p class="text-xs uppercase tracking-wide text-white/60">Order Date</p>
                            <p class="text-lg font-semibold">{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y H:i') }}</p>
                </div>
                        @php
                            $statusClass = match($order->status) {
                                'pending' => 'bg-amber-500/15 text-amber-200 border border-amber-400/40',
                                'paid', 'completed', 'delivered' => 'bg-emerald-500/15 text-emerald-100 border border-emerald-400/40',
                                'shipped' => 'bg-sky-500/15 text-sky-100 border border-sky-400/40',
                                'cancelled' => 'bg-red-500/15 text-red-100 border border-red-400/40',
                                default => 'bg-white/10 text-white border border-white/15',
                            };
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1.5 text-sm rounded-full {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                        </div>
                </div>
            </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-4">
            <div>
                        <p class="text-xs uppercase tracking-wide text-white/60">Shipping</p>
                        <h2 class="text-lg font-semibold">Shipping Information</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-white/80">
                        <p><span class="text-white/60">Name:</span> {{ $order->shipping_name }}</p>
                        <p><span class="text-white/60">Email:</span> {{ $order->shipping_email }}</p>
                        <p><span class="text-white/60">Phone:</span> {{ $order->shipping_phone }}</p>
                        <p class="sm:col-span-2"><span class="text-white/60">Address:</span> {{ $order->shipping_address }}</p>
                </div>
            </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-4">
            <div>
                        <p class="text-xs uppercase tracking-wide text-white/60">Items</p>
                        <h2 class="text-lg font-semibold">Order Items</h2>
                    </div>
                    <div class="divide-y divide-white/10">
                    @foreach ($order->products as $product)
                            <div class="py-4 flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <p class="font-semibold">{{ $product->product_name }}</p>
                                @if($product->pivot->variation_id)
                                    @php
                                        $variation = \App\Models\Variation::find($product->pivot->variation_id);
                                    @endphp
                                    @if($variation)
                                            <p class="text-sm text-white/70">Variation: {{ $variation->variation_name }}</p>
                                        @endif
                                    @endif
                                    <p class="text-sm text-white/60">Quantity: {{ $product->pivot->quantity }}</p>
                                    <p class="text-sm text-white/60">Unit: ₱{{ number_format($product->pivot->price_at_purchase, 2) }}</p>
                                </div>
                                <p class="font-semibold text-right">₱{{ number_format($product->pivot->price_at_purchase * $product->pivot->quantity, 2) }}</p>
                            </div>
                        @endforeach
                        </div>
                </div>
            </div>

            <!-- Right column -->
            <div class="space-y-6">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-white/60">Summary</p>
                        <h2 class="text-lg font-semibold">Payment Breakdown</h2>
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
                </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg shadow-black/30 space-y-3">
                    <p class="text-sm text-white/70">
                        Need help with this order? Contact support or your seller for updates.
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('orders.index') }}" class="flex-1 text-center px-4 py-2 rounded-md border border-white/15 hover:bg-white/5 transition text-sm">Back to Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

