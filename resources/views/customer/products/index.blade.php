<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<body class="bg-[#0f172a] text-white min-h-screen">
    <header class="w-full border-b border-white/5 bg-[#0b1220]/60 backdrop-blur relative z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-semibold">TH</div>
                <div>
                    <h1 class="text-lg font-semibold">TechHive</h1>
                    <p class="text-xs text-white/70">Future-ready gadgets for everyone</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">
                    Cart ({{ count(session('cart', [])) }})
                </a>
                @auth
                    @include('components.notification-bell')
                    @if(auth()->user()->role === 'seller')
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Dashboard</a>
                    @else
                        <a href="{{ route('wishlist.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Wishlist</a>
                        <a href="{{ route('orders.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">My Orders</a>
                    @endif
                    <span class="text-sm text-white/80">Hi, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline-block">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-16">
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

        <section class="mt-8">
            <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, #fff 0, transparent 25%), radial-gradient(circle at 80% 0%, #fff 0, transparent 20%);"></div>
                <div class="relative px-8 py-12 md:px-12 md:py-16 flex flex-col lg:flex-row gap-10 items-center">
                    <div class="flex-1">
                        <p class="uppercase tracking-[0.2em] text-white/80 text-xs mb-3">Fresh arrivals</p>
                        <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-4">Discover the latest tech drops on TechHive</h2>
                        <p class="text-white/80 max-w-2xl mb-6">Browse curated gadgets, compare variants, and find the right price range quickly with our revamped experience.</p>
                        <div class="flex flex-wrap gap-3">
                            <a href="#products" class="px-5 py-3 bg-white text-[#0f172a] font-semibold rounded-md shadow hover:shadow-lg transition">Shop latest</a>
                            <a href="#categories" class="px-5 py-3 border border-white/30 text-white rounded-md hover:bg-white/10 transition">Shop by category</a>
                        </div>
                    </div>
                    <div class="w-full lg:w-80">
                        <div class="bg-white/10 border border-white/20 rounded-xl p-6 backdrop-blur">
                            <p class="text-sm text-white/80 mb-3">Quick search</p>
                            <form method="GET" action="{{ route('shop.index') }}" class="space-y-3">
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full px-4 py-3 bg-white text-[#0f172a] rounded-md border border-white/20 focus:outline-none focus:ring-2 focus:ring-white" />
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[#0f172a]/60">⌕</div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min price" min="0" step="0.01" class="w-full px-3 py-2 bg-white text-[#0f172a] rounded-md border border-white/20 focus:outline-none focus:ring-2 focus:ring-white">
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max price" min="0" step="0.01" class="w-full px-3 py-2 bg-white text-[#0f172a] rounded-md border border-white/20 focus:outline-none focus:ring-2 focus:ring-white">
                                </div>
                                <div>
                                    <select name="stock_filter" class="w-full px-3 py-2 bg-white text-[#0f172a] rounded-md border border-white/20 focus:outline-none focus:ring-2 focus:ring-white">
                                        <option value="">Any stock</option>
                                        <option value="in_stock" {{ request('stock_filter') === 'in_stock' ? 'selected' : '' }}>In stock only</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full px-4 py-3 bg-[#0f172a] text-white rounded-md font-semibold hover:bg-[#0b1220] transition">Apply filters</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="categories" class="mt-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Shop by Category</h3>
                <a href="{{ route('shop.index') }}" class="text-sm text-blue-200 hover:text-white">View all</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                @forelse($categories as $category)
                    <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $category->id])) }}"
                       class="group border border-white/10 rounded-lg p-4 bg-white/5 hover:bg-white/10 transition flex items-center gap-3">
                        <div class="w-10 h-10 rounded-md bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($category->category_name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold">{{ $category->category_name }}</p>
                            <p class="text-xs text-white/60">Explore</p>
                        </div>
                    </a>
                @empty
                    <p class="text-white/60 text-sm">No categories yet.</p>
                @endforelse
            </div>
        </section>

        <section id="products" class="mt-12">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">Latest</p>
                    <h3 class="text-2xl font-semibold">Products</h3>
                    <p class="text-sm text-white/60">{{ $products->total() }} items</p>
                </div>
                <form method="GET" action="{{ route('shop.index') }}" class="hidden md:flex items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="px-4 py-2 rounded-md bg-white text-[#0f172a] border border-white/30 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    <select name="category" class="px-3 py-2 rounded-md bg-white text-[#0f172a] border border-white/30 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    <button class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">Search</button>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="border border-white/10 rounded-lg p-10 text-center bg-white/5">
                    <p class="text-white/70">No products found. Try adjusting your filters.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        @php
                            $minPrice = $product->variations->min('price');
                            $maxPrice = $product->variations->max('price');
                            $primaryImage = $product->primaryImage ?? $product->productImages->first();
                            $imageUrl = $primaryImage ? Storage::url($primaryImage->image_path) : 'https://placehold.co/600x400?text=No+Image';
                            $stock = $product->variations->sum('stock_quantity');
                        @endphp
                        <a href="{{ route('shop.show', $product) }}" class="group border border-white/10 rounded-xl overflow-hidden bg-white/5 hover:bg-white/10 transition shadow-sm hover:shadow-lg">
                            <div class="relative">
                                <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" class="w-full h-52 object-cover transition group-hover:scale-[1.02]">
                                <div class="absolute top-3 left-3 px-3 py-1 text-xs rounded-full bg-white/20 backdrop-blur border border-white/30">
                                    {{ $product->category->category_name ?? 'Uncategorized' }}
                                </div>
                                @if($stock <= 5)
                                    <div class="absolute top-3 right-3 px-3 py-1 text-xs rounded-full bg-red-500/80 text-white">Low stock</div>
                                @endif
                            </div>
                            <div class="p-4 space-y-2">
                                <h4 class="text-lg font-semibold">{{ $product->product_name }}</h4>
                                <p class="text-sm text-white/70 line-clamp-2 min-h-[40px]">{{ $product->description }}</p>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="font-semibold">
                                        @if($minPrice !== null)
                                            ₱{{ number_format($minPrice, 2) }}
                                            @if($maxPrice && $maxPrice !== $minPrice)
                                                - ₱{{ number_format($maxPrice, 2) }}
                                            @endif
                                        @else
                                            ₱{{ number_format($product->price, 2) }}
                                        @endif
                                    </div>
                                    <span class="text-white/60">{{ $stock }} in stock</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </section>
    </main>
</body>
</html>
