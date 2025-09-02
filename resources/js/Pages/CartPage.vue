<template>
  <div>
    <h1>Your cart</h1>
    <div v-if="lines.length === 0">Cart is empty</div>
    <div v-for="line in lines" :key="line.product_id" class="flex justify-between">
      <div>{{ line.name }} (x{{ line.quantity }})</div>
      <div>RM {{ line.subtotal.toFixed(2) }}</div>
    </div>

    <div class="mt-4">
      <div>Total: RM {{ total.toFixed(2) }}</div>
      <button @click="checkout" :disabled="lines.length===0">Place order</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data(){ return { lines: [], total: 0 } },
  async mounted(){
    const res = await fetch('/cart', { headers: { Accept: 'application/json' }});
    const cart = await res.json();
    this.lines = Object.values(cart.items || {});
    this.total = cart.total || 0;
  },
  methods: {
    async checkout(){
      const res = await axios.post('/checkout');
      alert('Order placed: #' + res.data.id);
      this.lines = []; this.total = 0;
      // optionally redirect to orders page
    }
  }
}
</script>
