<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard - TechHive' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')
</head>
<body class="bg-[#0b1220] text-white min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#0f172a] border-r border-white/10 fixed inset-y-0 left-0 z-40">
            <div class="px-4 py-5 flex items-center gap-3 border-b border-white/10">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-semibold">TH</div>
                <div>
                    <p class="text-sm text-white/70">TechHive</p>
                    <p class="text-base font-semibold">Dashboard</p>
                </div>
            </div>
            <nav class="px-3 py-4 space-y-3 text-sm">
                <div>
                    <p class="uppercase text-xs text-white/50 mb-2">Main</p>
                    <a href="{{ route('dashboard') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Overview</a>
                </div>
                <div>
                    <p class="uppercase text-xs text-white/50 mb-2">Products</p>
                    <a href="{{ route('products.index') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('products.index') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">View Products</a>
                    <a href="{{ route('products.create') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('products.create') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Add Product</a>
                    <a href="{{ route('restocks.index') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('restocks.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Stock / Restock</a>
                </div>
                <div>
                    <p class="uppercase text-xs text-white/50 mb-2">Orders</p>
                    <a href="{{ route('seller.orders.index') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('seller.orders.index') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">All Orders</a>
                    @foreach(['pending','paid','shipped','delivered','cancelled'] as $st)
                        <a href="{{ route('seller.orders.index', ['status' => $st]) }}" class="block px-3 py-2 rounded-md hover:bg-white/5 text-white/70">{{ ucfirst($st) }}</a>
                    @endforeach
                </div>
                <div>
                    <p class="uppercase text-xs text-white/50 mb-2">Suppliers</p>
                    <a href="{{ route('suppliers.index') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('suppliers.index') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Supplier List</a>
                    <a href="{{ route('suppliers.create') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('suppliers.create') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Add Supplier</a>
                </div>
                <div>
                    <p class="uppercase text-xs text-white/50 mb-2">Reports</p>
                    <a href="{{ route('analytics.index') }}" class="nav-link block px-3 py-2 rounded-md hover:bg-white/5 {{ request()->routeIs('analytics.index') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Analytics</a>
                </div>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 min-h-screen pl-64 bg-[#0b1220]">
            <header class="w-full border-b border-white/5 bg-[#0b1220]/60 backdrop-blur relative z-30">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-semibold">{{ $header ?? 'Dashboard' }}</h1>
                        @isset($subtitle)
                            <p class="text-xs text-white/60">{{ $subtitle }}</p>
                        @endisset
                    </div>
                    <div class="flex items-center gap-3">
                        @include('components.notification-bell')
                        <span class="text-sm text-white/80">Hi, {{ Auth::user()->name ?? 'User' }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-sm bg-white/5">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-6 py-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

