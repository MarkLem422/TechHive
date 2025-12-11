<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Wishlist - TechHive</title>
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
                    <h1 class="text-xl font-semibold">My Wishlist</h1>
                    <p class="text-xs text-white/70">Save items to buy later</p>
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

        @if ($wishlists->isEmpty())
            <div class="border border-white/10 rounded-lg p-8 text-center bg-white/5">
                <p class="text-white/70 mb-4">Your wishlist is empty.</p>
                <a href="{{ route('shop.index') }}" class="inline-block px-6 py-2 bg-white text-[#0b1220] border border-white rounded-md hover:shadow-lg transition">
                    Browse Products
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($wishlists as $wishlist)
                    <div class="bg-white/5 border border-white/10 rounded-lg p-4 shadow-lg">
                        <a href="{{ route('shop.show', $wishlist->product) }}" class="block mb-3">
                            <h3 class="font-medium text-white mb-1 hover:underline">{{ $wishlist->product->product_name }}</h3>
                            @if($wishlist->variation)
                                <p class="text-sm text-white/70 mb-2">Variation: {{ $wishlist->variation->variation_name }}</p>
                                <p class="font-semibold text-white">₱{{ number_format($wishlist->variation->price, 2) }}</p>
                            @else
                                <p class="font-semibold text-white">₱{{ number_format($wishlist->product->price, 2) }}</p>
                            @endif
                        </a>
                        <div class="flex gap-2 mt-4">
                            <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 text-sm border border-red-300/60 text-red-200 hover:border-red-200 rounded-sm bg-red-500/10">
                                    Remove
                                </button>
                            </form>
                            <a href="{{ route('shop.show', $wishlist->product) }}" class="flex-1 px-4 py-2 text-sm text-center bg-indigo-500 text-white border border-indigo-600 rounded-sm hover:bg-indigo-600 transition">
                                View Product
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $wishlists->links() }}
            </div>
        @endif
    </main>
</body>
</html>

