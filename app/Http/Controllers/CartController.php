<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    public function index(Request $request)
    {
    
        return Inertia::render('CartPage');
    }

    protected function emptyCart(): array
    {
        return ['items' => [], 'count' => 0, 'total' => 0.00];
    }

    protected function getCart(): array
    {
        if (auth()->check()) {
            // user->cart is cast to array (or null)
            return auth()->user()->cart ?? $this->emptyCart();
        }

        return session('cart', $this->emptyCart());
    }

    protected function putCart(array $cart): array
    {
        // Recompute count & total to be safe
        $count = 0;
        $total = 0.0;
        foreach ($cart['items'] as $line) {
            $count += (int)($line['quantity'] ?? 0);
            $total += (float)($line['subtotal'] ?? 0);
        }

        $cart['count'] = $count;
        $cart['total'] = round($total, 2);

        // Always update session so guest -> login workflows are simpler
        session(['cart' => $cart]);

        // If user is logged in, persist to DB (JSON column via cast)
        if (auth()->check()) {
            $user = auth()->user();
            $user->cart = $cart;
            $user->save();
        }

        return $cart;
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1'
        ]);

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
                'subtotal' => 0.0,
            ];
        }

        $cart['items'][$id]['quantity'] += $qty;
        $cart['items'][$id]['subtotal'] = round($cart['items'][$id]['unit_price'] * $cart['items'][$id]['quantity'], 2);

        $cart = $this->putCart($cart);

        return response()->json([
            'ok' => true,
            'count' => $cart['count'],
            'total' => $cart['total'],
            'cart' => $cart,
        ]);
    }

    /**
     * Merge two carts: $a is base, $b is to-be-merged-in.
     * Quantities are added and subtotals recalculated.
     */
    protected function mergeCarts(array $a, array $b): array
    {
        $result = $a;

        foreach ($b['items'] as $id => $line) {
            if (! isset($result['items'][$id])) {
                // normalise line
                $result['items'][$id] = [
                    'product_id' => $line['product_id'],
                    'name' => $line['name'] ?? '',
                    'unit_price' => (float)($line['unit_price'] ?? 0),
                    'quantity' => 0,
                    'subtotal' => 0,
                ];
            }

            $result['items'][$id]['quantity'] += (int)($line['quantity'] ?? 0);
            $result['items'][$id]['unit_price'] = (float)$result['items'][$id]['unit_price'];
            $result['items'][$id]['subtotal'] = round($result['items'][$id]['unit_price'] * $result['items'][$id]['quantity'], 2);
        }

        // Use putCart to recompute final totals and persist session/db
        return $this->putCart($result);
    }

     public function update(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:0',
        ]);
        $cart = $this->getCart();
        $id = (string)$data['product_id'];

        if (! isset($cart['items'][$id])) {
            return response()->json(['ok' => false, 'message' => 'Item not in cart'], 404);
        }

        $qty = (int)$data['qty'];

        if ($qty <= 0) {
            // remove item
            unset($cart['items'][$id]);
        } else {
            $cart['items'][$id]['quantity'] = $qty;
            $cart['items'][$id]['subtotal'] = round($cart['items'][$id]['unit_price'] * $qty, 2);
        }

        $cart = $this->putCart($cart);

        return response()->json([
            'ok' => true,
            'cart' => $cart,
            'count' => $cart['count'],
            'total' => $cart['total'],
        ]);
    }

    /**
     * Remove a product from cart (by product_id)
     */
    public function remove(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = $this->getCart();
        $id = (string)$data['product_id'];

        if (isset($cart['items'][$id])) {
            unset($cart['items'][$id]);
            $cart = $this->putCart($cart);
        }

        return response()->json([
            'ok' => true,
            'cart' => $cart,
            'count' => $cart['count'],
            'total' => $cart['total'],
        ]);
    }

    /**
     * Clear the whole cart
     */
    public function clear(Request $request)
    {
        $cart = ['items' => [], 'count' => 0, 'total' => 0.00];
        // persist
        $cart = $this->putCart($cart);

        return response()->json([
            'ok' => true,
            'cart' => $cart
        ]);
    }

    public function json()
{
    $cart = $this->getCart();
    return response()->json($cart);
}

}
