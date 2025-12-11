@props([
    'active' => 'all',
    'route' => null,
])

@php
    $statuses = ['all', 'pending', 'paid', 'shipped', 'delivered', 'cancelled'];
    $baseRoute = $route ?? request()->route()->getName();
@endphp

<div class="w-full border-b border-white/10 bg-white/5 rounded-lg overflow-hidden">
    <div class="grid grid-cols-6 text-center text-sm font-medium text-white">
        @foreach($statuses as $status)
            @php
                $isActive = ($active === $status) || ($active === null && $status === 'all');
                $url = route($baseRoute, array_merge(request()->query(), ['status' => $status === 'all' ? 'all' : $status]));
            @endphp
            <button
                type="button"
                data-status="{{ $status }}"
                data-url="{{ $url }}"
                class="order-tab px-3 py-3 {{ $isActive ? 'font-semibold text-white border-b-2 border-orange-400' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                {{ ucfirst($status) }}
            </button>
        @endforeach
    </div>
</div>

