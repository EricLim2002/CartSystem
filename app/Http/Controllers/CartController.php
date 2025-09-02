<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected function getCart(): array
    {
        return session('cart', ['items' => [], 'count' => 0, 'total' => 0]);
    }

    protected function putCart(array $cart): array
    {
        $count = 0; $total = 0;
        foreach ($cart['items'] as $line) {
            $count += $line['quantity'];
            $total += $line['subtotal'];
        }
        $cart['count'] = $count;
        $cart['total'] = round($total, 2);
        session(['cart' => $cart]);
        return $cart;
    }

    public function add(Request $request)
    {
        $data = $request->validate(['product_id'=>'required|exists:products,id','qty'=>'nullable|integer|min:1']);
        $qty = $data['qty'] ?? 1;
        $product = Product::findOrFail($data['product_id']);

        $cart = $this->getCart();
        $id = (string)$product->id;

        if (! isset($cart['items'][$id])) {
            $cart['items'][$id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => (float)$product->price,
                'quantity' => 0,
                'subtotal' => 0,
            ];
        }

        $cart['items'][$id]['quantity'] += $qty;
        $cart['items'][$id]['subtotal'] = round($cart['items'][$id]['unit_price'] * $cart['items'][$id]['quantity'], 2);

        $cart = $this->putCart($cart);

        // return JSON for AJAX
        return response()->json([
            'ok' => true,
            'count' => $cart['count'],
            'total' => $cart['total'],
            'cart' => $cart,
        ]);
    }

    // update, clear, show methods similarly...
}
