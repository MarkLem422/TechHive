@extends('layouts.dashboard')

@php
    $header = 'Add Supplier';
    $subtitle = 'Create a new vendor entry';
@endphp

@section('content')
    <div class="space-y-6 max-w-3xl">
        @if ($errors->any())
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 text-red-100 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white/5 border border-white/10 rounded-2xl shadow-lg shadow-black/30 p-6 space-y-6">
            <div>
                <p class="text-xs uppercase tracking-wide text-white/60">Supplier</p>
                <h2 class="text-2xl font-semibold text-white">Create supplier profile</h2>
                <p class="text-sm text-white/60">Store the primary contact info for your vendor.</p>
            </div>

            <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Supplier Name <span class="text-red-300">*</span></label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" required
                        class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Contact Person</label>
                        <input type="text" name="contact" value="{{ old('contact') }}"
                            class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/15 text-white placeholder-white/40 focus:ring-2 focus:ring-amber-400 focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 text-black font-semibold border border-amber-400 rounded-md hover:bg-amber-400 transition">
                        Save Supplier
                    </button>
                    <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 text-sm rounded-md border border-white/15 hover:bg-white/5 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

