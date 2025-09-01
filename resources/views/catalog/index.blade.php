@extends('layouts.app')
@section('content')
  <form method="GET" action="{{ route('catalog.index') }}">
    <input name="q" value="{{ $q ?? '' }}" placeholder="Search products...">
    <button>Search</button>
  </form>

  <div class="grid">
    @foreach($products as $p)
      <div class="card">
        <h3>{{ $p->name }}</h3>
        <p>RM {{ number_format($p->price,2) }}</p>
        <button class="add-to-cart" data-id="{{ $p->id }}">Add to cart</button>
      </div>
    @endforeach
  </div>

  {{ $products->withQueryString()->links() }}
@endsection
