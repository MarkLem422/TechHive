<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<body class="bg-[#0b1220] text-white min-h-screen">
    <header class="w-full border-b border-white/5 bg-[#0b1220]/70 backdrop-blur relative z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-semibold">TH</div>
                <div>
                    <h1 class="text-xl font-semibold">Checkout</h1>
                    <p class="text-xs text-white/70">Confirm your order and shipping details</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Back to Cart</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-14">
        @if (session('error'))
            <div class="mt-4 mb-4 rounded-md border border-red-500/30 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf

            <!-- Shipping Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Shipping Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Full Name <span class="text-red-300">*</span></label>
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" required
                                class="w-full px-4 py-3 border border-white/20 bg-white text-[#0b1220] rounded-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Email <span class="text-red-300">*</span></label>
                                <input type="email" name="shipping_email" value="{{ old('shipping_email', auth()->user()->email ?? '') }}" required
                                    class="w-full px-4 py-3 border border-white/20 bg-white text-[#0b1220] rounded-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Phone <span class="text-red-300">*</span></label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone') }}" required
                                    class="w-full px-4 py-3 border border-white/20 bg-white text-[#0b1220] rounded-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Shipping Address <span class="text-red-300">*</span></label>
                            <textarea name="shipping_address" rows="4" required
                                class="w-full px-4 py-3 border border-white/20 bg-white text-[#0b1220] rounded-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">{{ old('shipping_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Payment Method <span class="text-red-300">*</span></h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-white/15 rounded-sm cursor-pointer bg-white/5 hover:border-white/30 transition">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-3 accent-indigo-500" required>
                            <div class="flex-1">
                                <div class="font-medium">Cash on Delivery (COD)</div>
                                <div class="text-sm text-white/70">Pay when you receive your order</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border border-white/15 rounded-sm cursor-pointer bg-white/5 hover:border-white/30 transition">
                            <input type="radio" name="payment_method" value="digital" class="mr-3 accent-indigo-500" required>
                            <div class="flex-1">
                                <div class="font-medium">Digital Payment</div>
                                <div class="text-sm text-white/70">Pay securely with Stripe/PayPal</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Order Items</h2>
                    <div class="space-y-3">
                        @foreach ($cartItems as $item)
                            @php
                                $primaryImage = $item['variation']->variantImages->first()->image_path
                                    ?? $item['product']->primaryImage->image_path
                                    ?? $item['product']->productImages->first()->image_path
                                    ?? null;
                                $imageUrl = $primaryImage ? Storage::url($primaryImage) : 'https://placehold.co/200x150?text=Item';
                            @endphp
                            <div class="flex items-center gap-4 py-2 border-b border-white/10">
                                <div class="w-20 h-16 rounded-md overflow-hidden border border-white/10 bg-white/5">
                                    <img src="{{ $imageUrl }}" alt="Item image" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-white">{{ $item['product']->product_name }}</p>
                                    <p class="text-sm text-white/70">{{ $item['variation']->variation_name }} × {{ $item['quantity'] }}</p>
                                    @if ($item['variation']->stock_quantity < 10)
                                        <p class="text-xs text-orange-200 mt-1">⚠️ Low stock: {{ $item['variation']->stock_quantity }} available</p>
                                    @endif
                                </div>
                                <p class="font-semibold text-white">₱{{ number_format($item['total'], 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
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
                        <div class="border-t border-white/10 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="font-semibold text-white">Total</span>
                                <span class="font-semibold text-white">₱{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-indigo-500 text-white border border-indigo-600 rounded-sm hover:bg-indigo-600 transition">
                        Place Order
                    </button>
                    <a href="{{ route('cart.index') }}" class="block w-full text-center mt-2 px-6 py-2 border border-white/10 hover:border-white/30 rounded-sm text-sm bg-white/5 text-white">
                        Back to Cart
                    </a>
                </div>
            </div>
        </form>
    </main>
</body>
</html>

