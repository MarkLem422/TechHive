<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications - TechHive</title>
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
                    <h1 class="text-xl font-semibold">Notifications</h1>
                    <p class="text-xs text-white/70">All your updates in one place</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->role === 'seller')
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-sm bg-white/5">Dashboard</a>
                    @else
                        <a href="{{ route('shop.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-sm bg-white/5">Shop</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 pb-14">
        @if (session('success'))
            <div class="mt-4 mb-4 rounded-md border border-green-500/30 bg-green-500/10 text-green-100 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">All Notifications</h2>
            @if($notifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm border border-white/20 hover:border-white/40 rounded-sm bg-white/10 text-white">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        @if ($notifications->isEmpty())
            <div class="border border-white/10 rounded-lg p-8 text-center bg-white/5">
                <p class="text-white/70">No notifications yet.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($notifications as $notification)
                    <div class="border border-white/10 rounded-lg p-4 shadow-lg bg-white/5 {{ !$notification->is_read ? 'border-blue-300/40 bg-blue-500/10' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-white mb-1">{{ $notification->title }}</h3>
                                <p class="text-sm text-white/80 mb-2">{{ $notification->message }}</p>
                                <p class="text-xs text-white/60">{{ $notification->created_at->diffForHumans() }}</p>
                                @if($notification->order_id)
                                    <a href="{{ route('notifications.open', $notification) }}" class="text-sm text-blue-200 hover:text-white mt-2 inline-block font-semibold">
                                        Manage Order →
                                    </a>
                                @endif
                            </div>
                            @if(!$notification->is_read)
                                <form action="{{ route('notifications.mark-read', $notification) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs border border-white/20 hover:border-white/40 rounded-sm bg-white/10 text-white">
                                        Mark Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </main>
</body>
</html>

