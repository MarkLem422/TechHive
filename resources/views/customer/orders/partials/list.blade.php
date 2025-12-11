@php
    use Illuminate\Support\Facades\Storage;
@endphp

@if ($orders->isEmpty())
    <div class="border border-white/10 rounded-lg p-8 text-center bg-white/5">
        <p class="text-white/70 mb-4">No orders found.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach ($orders as $order)
            @php
                $firstProduct = $order->products->first();
                $primaryImage = $firstProduct?->primaryImage?->image_path
                    ?? $firstProduct?->productImages->first()->image_path
                    ?? null;
                $imageUrl = $primaryImage ? Storage::url($primaryImage) : 'https://placehold.co/200x150?text=Order';
            @endphp
            <div class="bg-white/5 border border-white/10 rounded-lg p-6 shadow-lg flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-40 h-28 rounded-md overflow-hidden border border-white/10 bg-white/5 shrink-0">
                    <img src="{{ $imageUrl }}" alt="Order image" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-white">Order #{{ $order->id }}</h3>
                            <p class="text-sm text-white/70">{{ $order->order_date->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-white">₱{{ number_format($order->total_amount, 2) }}</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-sm font-medium
                                @if($order->status === 'pending') bg-yellow-400/20 text-yellow-200
                                @elseif($order->status === 'paid') bg-blue-400/20 text-blue-200
                                @elseif($order->status === 'shipped') bg-purple-400/20 text-purple-200
                                @elseif($order->status === 'delivered') bg-green-400/20 text-green-200
                                @elseif($order->status === 'cancelled') bg-red-400/20 text-red-200
                                @else bg-white/10 text-white
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-sm text-white/80">
                        {{ $order->products->sum('pivot.quantity') }} item(s)
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 text-sm bg-indigo-500 text-white rounded-md border border-indigo-600 hover:bg-indigo-600 transition">
                            View Details →
                        </a>
                        @if(!in_array($order->status, ['shipped','delivered','cancelled']))
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel order #{{ $order->id }}?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm border border-red-300/60 text-red-200 rounded-md bg-red-500/10 hover:border-red-200 transition">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endif

