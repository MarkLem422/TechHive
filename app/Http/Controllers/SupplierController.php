<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function authorizeSeller(): ?RedirectResponse
    {
        if (Auth::user()?->role !== 'seller') {
            return redirect('/')->with('error', 'Access denied. Seller account required.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $suppliers = Supplier::orderBy('supplier_name')->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function edit(Supplier $supplier)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}

