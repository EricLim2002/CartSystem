<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // place an order from session cart
    public function store(Request $request)
    {
        $cart = session('cart');
        if (! $cart || empty($cart['items'])) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        DB::beginTransaction();
        try {
            $total = round($cart['total'] ?? 0, 2);

            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'pending'
            ]);

            foreach ($cart['items'] as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product_id'],
                    'product_name' => $line['name'],
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['subtotal'],
                ]);

                // optional: decrement stock safely if you want
                if ($product = Product::find($line['product_id'])) {
                    $newStock = max(0, $product->stock - $line['quantity']);
                    $product->update(['stock' => $newStock]);
                }
            }

            DB::commit();
            session()->forget('cart');

            return response()->json($order->load('items'), 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Could not place order', 'error' => $e->getMessage()], 500);
        }
    }

    // simple list (for admin or authenticated user)
    public function index(Request $request)
    {
        // If you want users to see only their orders:
        // $orders = Order::with('items')->where('user_id', auth()->id())->latest()->paginate(20);

        // For admin view:
        $orders = Order::with('items')->latest()->paginate(20);
        return view('orders', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,shipped,completed,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Order status updated');
    }
}
