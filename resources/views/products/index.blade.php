@extends('layouts.dashboard', ['title' => 'Products - TechHive', 'header' => 'Products', 'subtitle' => 'Manage your catalog'])

@section('content')
    @if (session('success'))
        <div class="mb-4 rounded-md border border-green-500/30 bg-green-500/10 text-green-100 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md border border-red-500/30 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Product List</h2>
        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-500 border border-indigo-600 rounded-sm hover:bg-indigo-600 transition">
            + Add Product
        </a>
    </div>

    <div class="overflow-hidden border border-white/10 rounded-lg bg-white/5 shadow-lg">
        <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wide">Stock</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-white/70 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($products as $product)
                    @php
                        $stockQty = $product->variations->sum('stock_quantity');
                        $minPrice = $product->variations->min('price');
                    @endphp
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-white">
                            <a href="{{ route('products.show', $product) }}" class="hover:underline">
                                {{ $product->product_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-white/70">{{ $product->category->category_name ?? 'Uncategorized' }}</td>
                        <td class="px-4 py-3 text-sm text-white">
                            @if($minPrice !== null)
                                From ₱{{ number_format($minPrice, 2) }}
                            @else
                                ₱{{ number_format($product->price, 2) }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-white">
                            {{ $stockQty }}
                            @if($stockQty < 5)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium text-red-200 bg-red-500/20 border border-red-300/50 rounded-sm">Low Stock</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="px-3 py-1.5 text-sm border border-white/20 hover:border-white/40 rounded-sm text-white">Edit</a>
                                <a href="{{ route('restocks.create') }}?product_id={{ $product->id }}" class="px-3 py-1.5 text-sm border border-white/20 hover:border-white/40 rounded-sm bg-white/5 text-white">Restock</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-sm border border-red-300/60 text-red-200 hover:border-red-200 rounded-sm bg-red-500/10">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-white/70">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection
