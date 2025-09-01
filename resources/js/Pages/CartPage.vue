<template>
  <div>
    <h2>Your Cart</h2>

    <div v-if="cartItems.length === 0">
      <p>Your cart is empty.</p>
    </div>

    <div v-else>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Subtotal</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in cartItems" :key="item.id">
            <td>{{ item.name }}</td>
            <td>
              <input type="number" v-model.number="item.qty" min="1" @change="updateItem(item)">
            </td>
            <td>RM {{ item.price.toFixed(2) }}</td>
            <td>RM {{ item.subtotal.toFixed(2) }}</td>
            <td>
              <button @click="removeItem(item)">Remove</button>
            </td>
          </tr>
        </tbody>
      </table>

      <p>Total: RM {{ total.toFixed(2) }}</p>
      <button @click="placeOrder">Place Order</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      cartItems: [],
    };
  },
  computed: {
    total() {
      return this.cartItems.reduce((sum, item) => sum + item.subtotal, 0);
    },
  },
  mounted() {
    this.fetchCart();
  },
  methods: {
    fetchCart() {
      axios.get('/cart/data') // new backend route to return full cart
        .then(res => {
          this.cartItems = res.data.cart;
        });
    },
    updateItem(item) {
      axios.post(`/cart/update/${item.id}`, { qty: item.qty })
        .then(res => {
          item.subtotal = item.qty * item.price;
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: res.data.totalItems } }));
        });
    },
    removeItem(item) {
      axios.post(`/cart/remove/${item.id}`)
        .then(res => {
          this.cartItems = this.cartItems.filter(i => i.id !== item.id);
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: res.data.totalItems } }));
        });
    },
    placeOrder() {
      axios.post('/cart/place-order')
        .then(() => {
          this.cartItems = [];
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: 0 } }));
          alert('Order placed!');
        });
    },
  },
};
</script>
