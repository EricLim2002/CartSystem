<nav>
  <a href="{{ route('catalog.index') }}">Catalog</a>
  <a href="{{ route('cart.index') }}">
    Cart <span id="cart-count">@{{ cartCount }}</span>
  </a>
  @auth
    <a href="{{ route('orders.index') }}">Orders</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button>Logout</button></form>
  @else
    <a href="{{ route('login') }}">Login</a>
  @endauth
</nav>
