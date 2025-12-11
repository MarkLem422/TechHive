<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create Account - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'custom-bg': '#FDFDFC',
                            'custom-text': '#1b1b18',
                            'custom-gray': '#706f6c',
                            'custom-border': '#e3e3e0',
                        }
                    }
                }
            }
        </script>
        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-[#FDFDFC] text-[#1b1b18] flex p-6 lg:p-8 items-center justify-center min-h-screen flex-col">
        <header class="w-full max-w-md text-sm mb-6">
            <nav class="flex items-center justify-end gap-4">
                <a
                    href="{{ url('/') }}"
                    class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm text-sm leading-normal"
                >
                    Home
                </a>
                <a
                    href="{{ route('login') }}"
                    class="inline-block px-5 py-1.5 border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] rounded-sm text-sm leading-normal"
                >
                    Sign In
                </a>
            </nav>
        </header>

        <main class="w-full max-w-md">
            <div class="bg-white shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] rounded-lg p-6 lg:p-8">
                <h1 class="mb-1 font-semibold text-base mb-4">Create Account</h1>

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-sm bg-red-50 border border-red-200">
                        <ul class="text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">
                            Name
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            class="w-full px-4 py-2 border border-[#e3e3e0] bg-white rounded-sm text-[#1b1b18] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] transition-all"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            class="w-full px-4 py-2 border border-[#e3e3e0] bg-white rounded-sm text-[#1b1b18] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] transition-all"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-2">
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border border-[#e3e3e0] bg-white rounded-sm text-[#1b1b18] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] transition-all"
                        >
                        <p class="mt-1 text-xs text-[#706f6c]">Must be at least 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2">
                            Confirm Password
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border border-[#e3e3e0] bg-white rounded-sm text-[#1b1b18] focus:outline-none focus:ring-2 focus:ring-[#1b1b18] transition-all"
                        >
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="w-full px-5 py-2 bg-[#1b1b18] text-white border border-black rounded-sm font-medium hover:bg-black transition-all"
                        >
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <p class="text-sm text-[#706f6c]">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-[#1b1b18] font-medium hover:underline">
                            Sign In
                        </a>
                    </p>
                </div>
            </div>
        </main>
    </body>
</html>

