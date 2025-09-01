<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller {
    // simple list view: for admin show all, otherwise show user's orders
    public function index() {
        if (auth()->user() && auth()->user()->is_admin ?? false) {
            $orders = Order::with('items','user')->latest()->paginate(15);
        } else {
            $orders = Order::with('items')->where('user_id', auth()->id())->latest()->paginate(15);
        }
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order) {
        // optional: gate check
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order) {
        $this->validate($request, ['status'=>'required|in:pending,processing,shipped,completed,cancelled']);
        $order->update(['status'=>$request->status]);
        return redirect()->back()->with('success','Status updated.');
    }
}
