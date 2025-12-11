@extends('layouts.dashboard')

@php
    $header = 'Suppliers';
    $subtitle = 'Manage vendor relationships and contacts';
@endphp

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 text-emerald-100 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-white/60">Directory</p>
                <h2 class="text-2xl font-semibold text-white">Supplier List</h2>
            </div>
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-md border border-emerald-400/40 bg-emerald-500/20 text-emerald-50 hover:bg-emerald-400/30 transition">
                <span class="text-lg leading-none">+</span> Add Supplier
            </a>
        </div>

        <div class="overflow-hidden border border-white/10 rounded-xl bg-white/5 backdrop-blur">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-white/60 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Contact</th>
                        <th class="px-4 py-3 text-left font-semibold">Phone</th>
                        <th class="px-4 py-3 text-left font-semibold">Address</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($suppliers as $supplier)
                        <tr class="hover:bg-white/[0.04] transition">
                            <td class="px-4 py-3 font-semibold text-white">{{ $supplier->supplier_name }}</td>
                            <td class="px-4 py-3 text-white/70">{{ $supplier->contact ?: '—' }}</td>
                            <td class="px-4 py-3 text-white/70">{{ $supplier->phone ?: '—' }}</td>
                            <td class="px-4 py-3 text-white/70">{{ $supplier->address ?: '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="px-3 py-1.5 rounded-md border border-white/15 bg-white/5 text-white/90 hover:bg-white/10 transition">Edit</a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete this supplier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-md border border-red-400/40 text-red-200 hover:bg-red-500/10 transition">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-white/60">No suppliers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-white/70">
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection

