<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b1220] text-white min-h-screen">
    <header class="w-full border-b border-white/5 bg-[#0b1220]/70 backdrop-blur relative z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-semibold">TH</div>
                <div>
                    <h1 class="text-xl font-semibold">Shopping Cart</h1>
                    <p class="text-xs text-white/70">Review items before checkout</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Continue Shopping</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-14">
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

        @if (empty($cartItems))
            <div class="border border-white/10 rounded-lg p-8 text-center bg-white/5">
                <p class="text-white/70 mb-4">Your cart is empty.</p>
                <a href="{{ route('shop.index') }}" class="inline-block px-6 py-2 bg-white text-[#0b1220] border border-white rounded-md hover:shadow-lg transition">
                    Browse Products
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($cartItems as $item)
                        <div class="bg-white/5 border border-white/10 rounded-lg p-4 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="w-28 h-28 rounded-md overflow-hidden border border-white/10 bg-white/5 shrink-0">
                                    <img src="{{ $item['image_url'] }}" alt="Product image" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="font-medium text-white mb-1">
                                                <a href="{{ route('shop.show', $item['product']) }}" class="hover:underline">
                                                    {{ $item['product']->product_name }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-white/70 mb-2">
                                                Variation: {{ $item['variation']->variation_name }}
                                            </p>
                                            <p class="text-sm font-semibold text-white">
                                                ₱{{ number_format($item['price'], 2) }} each
                                            </p>
                                        </div>
                                        <p class="text-sm font-semibold text-white text-right w-24">
                                            ₱{{ number_format($item['total'], 2) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 mt-3">
                                        <form action="{{ route('cart.update', $item['key']) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['variation']->stock_quantity }}"
                                                class="w-20 px-2 py-1 border border-white/20 bg-white text-[#0b1220] rounded-sm text-sm">
                                            <button type="submit" class="px-3 py-1 text-sm border border-white/20 hover:border-white/40 rounded-sm bg-white/10 text-white">
                                                Update
                                            </button>
                                        </form>
                                        <form action="{{ route('cart.remove', $item['key']) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 text-sm border border-red-300/60 text-red-200 hover:border-red-200 rounded-sm bg-red-500/10">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-xl sticky top-6">
                        <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-white/70">Subtotal</span>
                                <span class="text-white">₱{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-white/70">Tax (12%)</span>
                                <span class="text-white">₱{{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-white/70">Shipping</span>
                                <span class="text-white">₱{{ number_format($shipping, 2) }}</span>
                            </div>
                            <div class="border-t border-[#e3e3e0] pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-white">Total</span>
                                    <span class="font-semibold text-white">₱{{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="block w-full text-center px-6 py-3 bg-indigo-500 text-white border border-indigo-600 rounded-md hover:bg-indigo-600 transition">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </main>
</body>
</html>

