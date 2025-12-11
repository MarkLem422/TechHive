<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] min-h-screen">
    <header class="w-full border-b border-[#e3e3e0] bg-white mb-6">
        <div class="max-w-7xl mx-auto p-6">
            <h1 class="text-xl font-semibold">Order Confirmation</h1>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 pb-10">
        @if (session('success'))
            <div class="mb-6 rounded-sm border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-[#e3e3e0] rounded-lg p-8 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] mb-6">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold mb-2">Order Placed Successfully!</h2>
                <p class="text-[#706f6c]">Thank you for your order. We've received your order and will begin processing it right away.</p>
            </div>

            <div class="border-t border-[#e3e3e0] pt-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-2">Order Information</h3>
                        <div class="space-y-1 text-sm">
                            <p><span class="text-[#706f6c]">Order ID:</span> <span class="font-medium">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                            <p><span class="text-[#706f6c]">Order Date:</span> <span class="font-medium">{{ $order->order_date->format('M d, Y h:i A') }}</span></p>
                            <p><span class="text-[#706f6c]">Status:</span> 
                                <span class="inline-block px-2 py-1 text-xs rounded-sm font-medium
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                            <p><span class="text-[#706f6c]">Payment Method:</span> <span class="font-medium">{{ strtoupper($order->payment_method) }}</span></p>
                            <p><span class="text-[#706f6c]">Payment Status:</span> 
                                <span class="inline-block px-2 py-1 text-xs rounded-sm font-medium
                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2">Shipping Information</h3>
                        <div class="space-y-1 text-sm">
                            <p class="font-medium">{{ $order->shipping_name }}</p>
                            <p class="text-[#706f6c]">{{ $order->shipping_email }}</p>
                            <p class="text-[#706f6c]">{{ $order->shipping_phone }}</p>
                            <p class="text-[#706f6c]">{{ $order->shipping_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-[#e3e3e0] pt-6 mb-6">
                <h3 class="font-semibold mb-4">Order Items</h3>
                <div class="space-y-3">
                    @foreach ($order->products as $product)
                        @php
                            $variation = \App\Models\Variation::find($product->pivot->variation_id);
                        @endphp
                        <div class="flex justify-between items-center py-3 border-b border-[#e3e3e0]">
                            <div>
                                <p class="font-medium">{{ $product->product_name }}</p>
                                @if($variation)
                                    <p class="text-sm text-[#706f6c]">{{ $variation->variation_name }} × {{ $product->pivot->quantity }}</p>
                                @else
                                    <p class="text-sm text-[#706f6c]">Quantity: {{ $product->pivot->quantity }}</p>
                                @endif
                            </div>
                            <p class="font-semibold">₱{{ number_format($product->pivot->price_at_purchase * $product->pivot->quantity, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-[#e3e3e0] pt-6">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-[#706f6c]">Subtotal</span>
                        <span class="text-[#1b1b18]">₱{{ number_format($order->total_amount - $order->tax - $order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#706f6c]">Tax (12%)</span>
                        <span class="text-[#1b1b18]">₱{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#706f6c]">Shipping</span>
                        <span class="text-[#1b1b18]">₱{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="border-t border-[#e3e3e0] pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="font-semibold text-lg">Total</span>
                            <span class="font-semibold text-lg">₱{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-4 justify-center">
            <a href="{{ route('shop.index') }}" class="px-6 py-2 border border-[#19140035] hover:border-[#1915014a] rounded-sm">
                Continue Shopping
            </a>
            <a href="{{ route('orders.show', $order) }}" class="px-6 py-3 bg-[#1b1b18] text-white border border-black rounded-sm hover:bg-black transition">
                View Order Details
            </a>
        </div>
    </main>
</body>
</html>

