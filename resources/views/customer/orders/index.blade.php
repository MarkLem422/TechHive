<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Orders - TechHive</title>
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
                    <h1 class="text-xl font-semibold">My Orders</h1>
                    <p class="text-xs text-white/70">Track your purchases</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @include('components.notification-bell')
                <a href="{{ route('shop.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Shop</a>
                <a href="{{ route('cart.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Cart</a>
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

        <div class="space-y-4">
            <x-order-status-tabs :active="$activeStatus ?? 'all'" :route="'orders.index'" />
            <div id="orders-list">
                @include('customer.orders.partials.list', ['orders' => $orders])
            </div>
        </div>

        @if ($orders->isEmpty())
            <div class="border border-white/10 rounded-lg p-8 text-center bg-white/5">
                <p class="text-white/70 mb-4">You have no orders yet.</p>
                <a href="{{ route('shop.index') }}" class="inline-block px-6 py-2 bg-white text-[#0b1220] border border-white rounded-sm hover:shadow-lg transition">
                    Start Shopping
                </a>
            </div>
        @endif
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.order-tab');
            const listEl = document.getElementById('orders-list');

            const setActive = (clicked) => {
                tabs.forEach(t => t.classList.remove('font-semibold', 'text-white', 'border-b-2', 'border-orange-400'));
                clicked.classList.add('font-semibold', 'text-white', 'border-b-2', 'border-orange-400');
            };

            const fetchList = (url, fallback) => {
                const u = new URL(url, window.location.origin);
                u.searchParams.set('ajax', '1');
                fetch(u, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => { listEl.innerHTML = html; })
                    .catch(() => { window.location = fallback; });
            };

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const url = tab.dataset.url;
                    setActive(tab);
                    fetchList(url, url);
                });
            });
        });
    </script>
</body>
</html>

