<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
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

        $categories = Category::withCount('products')->orderBy('category_name')->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        return view('categories.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($redirect = $this->authorizeSeller()) {
            return $redirect;
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}

