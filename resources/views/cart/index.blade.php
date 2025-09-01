@extends('layouts.app')
@section('content')
<h2>Your Cart</h2>
@if(empty($cart))
  <p>Your cart is empty.</p>
@else
  <table>
    <thead><tr><th>Product</th><th>Qty</th><th>Unit</th><th>Subtotal</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($cart as $id => $item)
        <tr>
          <td>{{ $item['name'] }}</td>
          <td>
            <form action="{{ route('cart.update', $id) }}" method="POST">
              @csrf
              <input type="number" name="qty" value="{{ $item['qty'] }}" min="1">
              <button>Update</button>
            </form>
          </td>
          <td>RM {{ number_format($item['price'],2) }}</td>
          <td>RM {{ number_format($item['subtotal'],2) }}</td>
          <td>
            <form action="{{ route('cart.remove', $id) }}" method="POST">@csrf<button>Remove</button></form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <p>Total: RM {{ number_format($total,2) }}</p>
  <form method="POST" action="{{ route('cart.placeOrder') }}">@csrf
    <button>Place Order</button>
  </form>
@endif
@endsection
