<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->product_name }} - TechHive</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $productImages = $product->productImages->map(fn($img) => Storage::url($img->image_path))->toArray();
    $placeholder = 'https://placehold.co/800x600?text=No+Image';
@endphp
<body class="bg-[#0b1220] text-white min-h-screen">
    <header class="w-full border-b border-white/5 bg-[#0b1220]/80 backdrop-blur">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">← Continue shopping</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="px-4 py-2 text-sm border border-white/10 hover:border-white/30 rounded-md bg-white/5">Cart ({{ count(session('cart', [])) }})</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 pb-16">
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

        <section class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-4">
                <div class="border border-white/10 rounded-xl bg-white/5 overflow-hidden">
                    <img id="main-image" src="{{ $productImages[0] ?? $placeholder }}" alt="{{ $product->product_name }}" class="w-full h-[420px] object-cover">
                </div>
                <div id="thumbs" class="grid grid-cols-5 gap-2">
                    @forelse($productImages as $img)
                        <button type="button" class="thumb border border-white/10 rounded-md overflow-hidden focus:outline-none">
                            <img src="{{ $img }}" class="w-full h-20 object-cover">
                        </button>
                    @empty
                        <div class="text-sm text-white/60 col-span-5">No images uploaded yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-white/60 mb-1">{{ $product->category->category_name ?? 'Uncategorized' }}</p>
                    <h1 class="text-3xl font-bold">{{ $product->product_name }}</h1>
                    <p class="text-white/70 mt-2">{{ $product->description }}</p>
                </div>

                @if ($product->variations->isEmpty())
                    <div class="border border-red-500/40 bg-red-500/10 text-red-100 px-4 py-3 rounded-md">
                        This product is currently unavailable.
                    </div>
                @else
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4 bg-white/5 border border-white/10 rounded-xl p-5">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="space-y-2">
                            <p class="text-sm font-semibold">Select variant</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($product->variations as $variation)
                                    <label class="border border-white/10 rounded-lg px-3 py-3 cursor-pointer hover:border-white/40 transition flex items-center gap-3 {{ $variation->stock_quantity === 0 ? 'opacity-60' : '' }}">
                                        <input
                                            type="radio"
                                            name="variation_id"
                                            value="{{ $variation->id }}"
                                            data-price="{{ $variation->price }}"
                                            data-stock="{{ $variation->stock_quantity }}"
                                            data-variant-images='@json($variation->variantImages->map(fn($img) => Storage::url($img->image_path))->values())'
                                            class="peer accent-indigo-500"
                                            {{ $variation->stock_quantity === 0 ? 'disabled' : '' }}>
                                        <div class="flex flex-col text-sm">
                                            <span class="font-semibold">{{ $variation->variation_name }}</span>
                                            <span class="text-white/70">₱{{ number_format($variation->price, 2) }}</span>
                                            <span class="text-xs text-white/60">
                                                @if($variation->stock_quantity == 0)
                                                    Out of stock
                                                @elseif($variation->stock_quantity < 5)
                                                    Low stock: {{ $variation->stock_quantity }}
                                                @else
                                                    Stock: {{ $variation->stock_quantity }}
                                                @endif
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 items-end">
                            <div>
                                <label class="block text-sm font-medium mb-1">Quantity</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" class="w-full px-4 py-3 rounded-md bg-white text-[#0b1220] border border-white/20 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <p class="text-xs text-white/70 mt-1" id="stock-info"></p>
                            </div>
                            <div class="border border-white/10 rounded-lg p-3 bg-white/5">
                                <p class="text-xs text-white/60">Price</p>
                                <p class="text-2xl font-semibold" id="price-display">
                                    @if($minPrice !== null)
                                        ₱{{ number_format($minPrice, 2) }} @if($maxPrice && $maxPrice !== $minPrice)<span class="text-sm text-white/70">- ₱{{ number_format($maxPrice, 2) }}</span>@endif
                                    @else
                                        ₱{{ number_format($product->price, 2) }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @auth
                                <button type="button" onclick="addToWishlist({{ $product->id }})" class="px-4 py-3 border border-white/20 hover:border-white/40 rounded-md bg-white/5">♡ Wishlist</button>
                            @endauth
                            <button type="submit" class="flex-1 px-6 py-3 bg-indigo-500 text-white font-semibold rounded-md hover:bg-indigo-600 transition">
                                Add to Cart
                            </button>
                        </div>
                    </form>
                @endif

                <div class="border border-white/10 rounded-xl p-5 bg-white/5">
                    <p class="text-sm font-semibold mb-2">Details</p>
                    <div class="text-sm text-white/70 space-y-1">
                        <p>Total stock: {{ $totalStock }}</p>
                        <p>Variants: {{ $product->variations->count() }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-12">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold">Reviews</h2>
                    @php
                        $avgRating = $product->averageRating();
                        $totalReviews = $product->totalReviews();
                    @endphp
                    <p class="text-sm text-white/60">
                        @if($totalReviews > 0)
                            {{ number_format($avgRating, 1) }} / 5.0 ({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})
                        @else
                            No reviews yet
                        @endif
                    </p>
                </div>
                @auth
                    <button onclick="document.getElementById('review-form').classList.toggle('hidden')" class="px-4 py-2 text-sm border border-white/20 hover:border-white/40 rounded-md bg-white/5">
                        Write a Review
                    </button>
                @endauth
            </div>

            @auth
                <div id="review-form" class="hidden mb-6 pb-6 border-b border-white/10">
                    <form action="{{ route('reviews.store', $product) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium mb-2">Rating</label>
                            <select name="rating" required class="w-full px-4 py-2 rounded-md bg-white text-[#0b1220] border border-white/20">
                                <option value="">Select rating</option>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Very Good</option>
                                <option value="3">3 - Good</option>
                                <option value="2">2 - Fair</option>
                                <option value="1">1 - Poor</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Comment</label>
                            <textarea name="comment" rows="4" class="w-full px-4 py-2 rounded-md bg-white text-[#0b1220] border border-white/20"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">Submit Review</button>
                        </div>
                    </form>
                </div>
            @endauth

            <div class="space-y-4">
                @forelse($product->reviews as $review)
                    <div class="border border-white/10 rounded-lg p-4 bg-white/5">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-medium">{{ $review->customer->first_name }} {{ $review->customer->last_name }}</p>
                                <div class="flex items-center gap-1 text-yellow-300">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-white/60">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-white/80">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-white/60">No reviews yet. Be the first to review this product!</p>
                @endforelse
            </div>
        </section>

        <section class="mt-12">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Related products</h3>
                <a href="{{ route('shop.index', ['category' => $product->category_id]) }}" class="text-sm text-blue-200 hover:text-white">See all</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($relatedProducts as $related)
                    @php
                        $rMin = $related->variations->min('price');
                        $rMax = $related->variations->max('price');
                        $rImage = $related->primaryImage ? Storage::url($related->primaryImage->image_path) : 'https://placehold.co/400x300?text=No+Image';
                    @endphp
                    <a href="{{ route('shop.show', $related) }}" class="border border-white/10 rounded-lg overflow-hidden bg-white/5 hover:bg-white/10 transition">
                        <img src="{{ $rImage }}" class="w-full h-36 object-cover">
                        <div class="p-3 space-y-1">
                            <p class="text-xs text-white/60">{{ $related->category->category_name ?? 'Uncategorized' }}</p>
                            <p class="font-semibold">{{ $related->product_name }}</p>
                            <p class="text-sm text-white/70">
                                @if($rMin !== null)
                                    ₱{{ number_format($rMin, 2) }} @if($rMax && $rMax !== $rMin)- ₱{{ number_format($rMax, 2) }}@endif
                                @else
                                    ₱{{ number_format($related->price, 2) }}
                                @endif
                            </p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-white/60">No related products yet.</p>
                @endforelse
            </div>
        </section>
    </main>

    <script>
        const variationInputs = document.querySelectorAll('input[name=\"variation_id\"]');
        const quantityInput = document.getElementById('quantity');
        const priceDisplay = document.getElementById('price-display');
        const stockInfo = document.getElementById('stock-info');
        const mainImage = document.getElementById('main-image');
        const thumbsContainer = document.getElementById('thumbs');
        const defaultImages = @json($productImages ?: [$placeholder]);
        const variantImagesMap = {};

        variationInputs.forEach((input) => {
            const images = input.dataset.variantImages ? JSON.parse(input.dataset.variantImages) : [];
            variantImagesMap[input.value] = images; // already absolute URLs from server
            input.addEventListener('change', handleVariantChange);
        });

        function renderGallery(images) {
            const list = images.length ? images : defaultImages;
            if (list.length) {
                mainImage.src = list[0];
            }
            if (thumbsContainer) {
                thumbsContainer.innerHTML = '';
                list.forEach((src) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'thumb border border-white/10 rounded-md overflow-hidden focus:outline-none';
                    btn.innerHTML = `<img src=\"${src}\" class=\"w-full h-20 object-cover\">`;
                    btn.addEventListener('click', () => mainImage.src = src);
                    thumbsContainer.appendChild(btn);
                });
            }
        }

        function handleVariantChange(e) {
            const target = e.target;
            const price = parseFloat(target.dataset.price);
            const stock = parseInt(target.dataset.stock);
            const images = variantImagesMap[target.value] || [];

            if (priceDisplay) {
                priceDisplay.textContent = '₱' + price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            if (quantityInput) {
                quantityInput.max = stock;
                quantityInput.disabled = stock === 0;
                if (stock > 0 && quantityInput.value > stock) {
                    quantityInput.value = stock;
                }
            }
            if (stockInfo) {
                stockInfo.textContent = stock === 0 ? 'Out of stock' : `${stock} available`;
                stockInfo.className = 'text-xs mt-1 ' + (stock === 0 ? 'text-red-300' : 'text-white/70');
            }
            renderGallery(images);
        }

        renderGallery(defaultImages);
    </script>

    <script>
        async function addToWishlist(productId) {
            try {
                const chosen = document.querySelector('input[name=\"variation_id\"]:checked');
                const response = await fetch('{{ route("wishlist.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        variation_id: chosen ? chosen.value : null
                    })
                });
                
                const data = await response.json();
                alert(data.message);
            } catch (error) {
                alert('Error adding to wishlist');
            }
        }
    </script>
</body>
</html>
