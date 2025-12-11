<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Category - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] min-h-screen">
    <header class="w-full border-b border-[#e3e3e0] bg-white mb-6">
        <div class="max-w-4xl mx-auto p-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Edit Category</h1>
                <p class="text-sm text-[#706f6c]">Update category details</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 text-sm border border-transparent hover:border-[#19140035] rounded-sm">Category List</a>
                <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm border border-transparent hover:border-[#19140035] rounded-sm">Dashboard</a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 pb-10">
        @if ($errors->any())
            <div class="mb-4 rounded-sm border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('categories.update', $category) }}" method="POST" class="bg-white border border-[#e3e3e0] rounded-lg p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-2">Category Name <span class="text-red-600">*</span></label>
                <input type="text" name="category_name" value="{{ old('category_name', $category->category_name) }}" required
                    class="w-full px-4 py-2 border border-[#e3e3e0] rounded-sm focus:ring-2 focus:ring-[#1b1b18] focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border border-[#e3e3e0] rounded-sm focus:ring-2 focus:ring-[#1b1b18] focus:outline-none">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-[#1b1b18] text-white border border-black rounded-sm hover:bg-black transition">
                    Update Category
                </button>
                <a href="{{ route('categories.index') }}" class="px-5 py-2 border border-[#19140035] hover:border-[#1915014a] rounded-sm text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </main>
</body>
</html>

