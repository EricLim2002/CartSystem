<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    // Catalogue + optional search
    public function index(Request $request)
    {
        $q = $request->query('q');

        $productsQuery = Product::when($q, function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%");
        })->orderBy('created_at', 'desc');

        // For suggestions return a small set
        if ($request->wantsJson() || $request->query('ajax')) {
            $items = $productsQuery->limit(8)->get(['id', 'name', 'price', 'slug']);
            return response()->json($items);
        }

        // normal Inertia page:
        $products = $productsQuery->paginate(12)->withQueryString();

        return Inertia::render('Catalogue', [
            'products' => $products,
            'filters' => ['q' => $q],
        ]);
    }

    public function show(Product $product)
    {
        return Inertia::render('Product/Show', [
            'product' => $product,
        ]);
    }
}
