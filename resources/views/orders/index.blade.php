@extends('layouts.app')
@section('content')
<h2>Orders</h2>
@foreach($orders as $order)
  <div class="order-card">
    <h3>Order #{{ $order->id }} - {{ $order->status }}</h3>
    <p>Total: RM {{ number_format($order->total,2) }}</p>
    <ul>
      @foreach($order->items as $it)
        <li>{{ $it->product_name }} x{{ $it->quantity }} — RM {{ number_format($it->subtotal,2) }}</li>
      @endforeach
    </ul>

    @if(auth()->user() && (auth()->user()->is_admin ?? false))
    <form method="POST" action="{{ route('orders.updateStatus',$order) }}">
      @csrf
      <select name="status">
        <option value="pending" @selected($order->status=='pending')>pending</option>
        <option value="processing" @selected($order->status=='processing')>processing</option>
        <option value="shipped" @selected($order->status=='shipped')>shipped</option>
        <option value="completed" @selected($order->status=='completed')>completed</option>
        <option value="cancelled" @selected($order->status=='cancelled')>cancelled</option>
      </select>
      <button>Update</button>
    </form>
    @endif
  </div>
@endforeach

{{ $orders->links() }}
@endsection
