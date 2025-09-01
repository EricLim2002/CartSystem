<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;

class CatalogController extends Controller {
    public function index(Request $request) {
        $q = $request->input('q');
        $query = Product::query();
        if ($q) {
            $query->where('name','like',"%{$q}%")
                  ->orWhere('description','like',"%{$q}%");
        }
        $products = $query->paginate(12);
        return view('catalog.index', compact('products','q'));
    }

    public function show(Product $product) {
        return view('catalog.show', compact('product'));
    }
}
