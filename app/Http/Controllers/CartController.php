<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Auth;
use DB;

class CartController extends Controller {
    public function index() {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum('subtotal');
        return view('cart.index', compact('cart','total'));
    }

    public function add(Request $request, $id) {
        $product = Product::findOrFail($id);
        $qty = max(1, (int)$request->input('qty',1));
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $qty;
            $cart[$id]['subtotal'] = $cart[$id]['qty'] * $cart[$id]['price'];
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => $qty,
                'subtotal' => $product->price * $qty,
            ];
        }
        session()->put('cart', $cart);
        return response()->json(['success'=>true,'count'=>array_sum(array_column($cart,'qty'))]);
    }

    public function update(Request $request, $id) {
        $qty = max(1, (int)$request->input('qty',1));
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['qty'] = $qty;
            $cart[$id]['subtotal'] = $cart[$id]['price'] * $qty;
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success','Cart updated.');
    }

    public function remove(Request $request, $id) {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success','Item removed.');
    }

    public function placeOrder(Request $request) {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error','Cart is empty.');
        }

        DB::transaction(function() use ($cart) {
            $total = collect($cart)->sum('subtotal');
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending'
            ]);
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
            // clear cart
            session()->forget('cart');
        });

        return redirect()->route('orders.index')->with('success','Order placed.');
    }
}
