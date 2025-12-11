<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categories - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] min-h-screen">
    <header class="w-full border-b border-[#e3e3e0] bg-white mb-6">
        <div class="max-w-7xl mx-auto p-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Categories</h1>
                <p class="text-sm text-[#706f6c]">Manage product categories</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm border border-transparent hover:border-[#19140035] rounded-sm">Dashboard</a>
                <a href="{{ url('/') }}" class="px-4 py-2 text-sm border border-transparent hover:border-[#19140035] rounded-sm">Home</a>
                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm border border-[#19140035] hover:border-[#1915014a] rounded-sm">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-10">
        @if (session('success'))
            <div class="mb-4 rounded-sm border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-sm border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Category List</h2>
            <div class="relative">
                <button id="category-menu-button" type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#1b1b18] border border-black rounded-sm hover:bg-black transition">
                    ☰ Actions
                </button>
                <div id="category-menu-panel" class="hidden absolute right-0 mt-2 w-44 bg-white border border-[#e3e3e0] rounded-sm shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_4px_6px_0px_rgba(0,0,0,0.08)] z-10">
                    <a href="{{ route('categories.create') }}" class="block px-4 py-2 text-sm hover:bg-[#f9f9f7]">Add Category</a>
                    <a href="{{ route('products.create') }}" class="block px-4 py-2 text-sm hover:bg-[#f9f9f7]">Add Product</a>
                </div>
            </div>
        </div>

        <div class="overflow-hidden border border-[#e3e3e0] rounded-lg bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)]">
            <table class="min-w-full divide-y divide-[#e3e3e0]">
                <thead class="bg-[#f9f9f7]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#706f6c] uppercase tracking-wide">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#706f6c] uppercase tracking-wide">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#706f6c] uppercase tracking-wide">Products</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-[#706f6c] uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-[#1b1b18]">{{ $category->category_name }}</td>
                            <td class="px-4 py-3 text-sm text-[#706f6c]">{{ $category->description ?: '—' }}</td>
                            <td class="px-4 py-3 text-sm text-[#1b1b18]">{{ $category->products_count }}</td>
                            <td class="px-4 py-3 text-sm text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="px-3 py-1.5 text-sm border border-[#19140035] hover:border-[#1915014a] rounded-sm">Edit</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-sm border border-red-200 text-red-700 hover:border-red-300 rounded-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-[#706f6c]">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </main>
</body>
</html>

<script>
    (() => {
        const btn = document.getElementById('category-menu-button');
        const panel = document.getElementById('category-menu-panel');
        if (!btn || !panel) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            panel.classList.add('hidden');
        });
    })();
</script>

