<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
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
        if (!$cart || empty($cart['items'])) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        DB::beginTransaction();
        $ordercount = Order::where('user_id', auth()->id())->count() + 1;

        $orderNo = 'SO' . $ordercount;
        try {
            $total = round($cart['total'] ?? 0, 2);

            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'pending',
                'order_no' => $orderNo

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

                if ($product = Product::find($line['product_id'])) {
                    $newStock = max(0, $product->stock - $line['quantity']);
                    $product->update(['stock' => $newStock]);
                }
            }

            DB::commit();
            session()->forget('cart');
            $user = auth()->user();
            $user->cart = null;
            $user->save();
            return response()->json($order->load('items'), 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Could not place order', 'error' => $e->getMessage()], 500);
        }
    }

    // simple list (for admin or authenticated user)
    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())->with('items')->latest()->paginate(20);
        return Inertia::render('Orders', [
            'orders' => $orders,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:completed,cancelled',
        ]);

        // Check if order is already final (completed or cancelled)
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order status cannot be changed once it is completed or cancelled.',
                'status' => $order->status,
            ], 403);
        }

        // Update the status
        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'status' => $order->status,
        ]);
    }

}
