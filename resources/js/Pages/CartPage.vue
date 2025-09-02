<template>
  <div class="cart-page container py-6">
    <h1 class="mb-4">Your cart</h1>

    <div v-if="loading" class="text-muted">Loading cart…</div>

    <div v-else>
      <div v-if="lines.length === 0" class="alert alert-info">
        Cart is empty
      </div>

      <div v-else>
        <div v-for="line in lines" :key="line.product_id" class="d-flex align-items-center justify-content-between py-2 border-bottom">
          <div>
            <div class="fw-semibold">{{ line.name }}</div>
            <div class="small text-muted">ID: {{ line.product_id }}</div>
            <div class="small text-muted">Unit: RM {{ formatMoney(line.unit_price) }}</div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <input type="number"
                   min="0"
                   class="form-control"
                   style="width:96px"
                   v-model.number="localQty[line.product_id]"
            />

            <button class="btn btn-sm btn-secondary" :disabled="processing[line.product_id]" @click="updateQty(line.product_id)">
              <span v-if="processing[line.product_id]">…</span>
              <span v-else>Update</span>
            </button>

            <div class="ms-3">RM {{ formatMoney(line.subtotal) }}</div>

            <button class="btn btn-sm btn-outline-danger ms-3" :disabled="processing[line.product_id]" @click="removeItem(line.product_id)">
              Remove
            </button>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
          <div>
            <button class="btn btn-outline-danger" @click="clearCart" :disabled="processingAll">Clear Cart</button>
            <button class="btn btn-light ms-2" @click="continueShopping">Continue shopping</button>
          </div>

          <div class="text-end">
            <div class="small text-muted">Items: {{ cart.count }}</div>
            <div class="fs-5 fw-semibold">Total: RM {{ formatMoney(cart.total) }}</div>
            <div class="mt-2">
              <button class="btn btn-primary" @click="checkout" :disabled="lines.length===0 || processingAll">Checkout</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="error" class="mt-3 alert alert-danger">
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      loading: true,
      cart: { items: {}, count: 0, total: 0 },
      lines: [],
      localQty: {},
      processing: {},  // per-product spinner state
      processingAll: false,
      error: null
    };
  },

  mounted() {
    this.loadCart();
  },

  methods: {
    formatMoney(v) {
      return Number(v || 0).toFixed(2);
    },

    async loadCart() {
      this.loading = true;
      this.error = null;
      try {
        const res = await axios.get('/cart/data'); // route we created
        // server should return JSON cart
        this.cart = res.data || { items: {}, count: 0, total: 0 };
        this.lines = Object.values(this.cart.items || {});
        this.initLocalQty();
      } catch (err) {
        this.handleRequestError(err, 'Failed to load cart');
      } finally {
        this.loading = false;
      }
    },

    initLocalQty() {
      this.localQty = {};
      this.lines.forEach(line => {
        this.localQty[line.product_id] = Number(line.quantity || 0);
      });
    },

    async updateQty(productId) {
      const qty = Number(this.localQty[productId] ?? 0);
      if (!Number.isInteger(qty) || qty < 0) {
        return alert('Quantity must be a non-negative integer');
      }

      // if qty === 0 we choose to remove the item server-side to keep API simple
      this.processing = { ...this.processing, [productId]: true };
      try {
        const res = await axios.post('/cart/update', {
          product_id: productId,
          qty: qty
        });

        if (res.data && res.data.ok) {
          this.cart = res.data.cart;
          this.lines = Object.values(this.cart.items || {});
          this.initLocalQty();
        } else {
          // server might respond { ok:false, message: '...' }
          throw new Error(res.data?.message || 'Update failed');
        }
      } catch (err) {
        this.handleRequestError(err, 'Could not update item');
      } finally {
        this.processing[productId] = false;
      }
    },

    async removeItem(productId) {
      if (!confirm('Remove this item from cart?')) return;
      this.processing = { ...this.processing, [productId]: true };
      try {
        const res = await axios.post('/cart/remove', { product_id: productId });
        if (res.data && res.data.ok) {
          this.cart = res.data.cart;
          this.lines = Object.values(this.cart.items || {});
          this.initLocalQty();
        } else {
          throw new Error(res.data?.message || 'Remove failed');
        }
      } catch (err) {
        this.handleRequestError(err, 'Could not remove item');
      } finally {
        this.processing[productId] = false;
      }
    },

    async clearCart() {
      if (!confirm('Clear entire cart?')) return;
      this.processingAll = true;
      try {
        const res = await axios.post('/cart/clear');
        if (res.data && res.data.ok) {
          this.cart = res.data.cart;
          this.lines = [];
          this.initLocalQty();
        } else {
          throw new Error(res.data?.message || 'Clear failed');
        }
      } catch (err) {
        this.handleRequestError(err, 'Could not clear cart');
      } finally {
        this.processingAll = false;
      }
    },

    async checkout() {
      if (!confirm('Place order now?')) return;
      this.processingAll = true;
      try {
        const res = await axios.post('/checkout'); // adapt to your checkout route
        if (res.data && res.data.id) {
          alert('Order placed: #' + res.data.id);
          this.cart = { items: {}, count: 0, total: 0 };
          this.lines = [];
          this.initLocalQty();
          // optionally redirect: window.location.href = `/orders/${res.data.id}`;
        } else {
          throw new Error(res.data?.message || 'Checkout failed');
        }
      } catch (err) {
        this.handleRequestError(err, 'Could not place order');
      } finally {
        this.processingAll = false;
      }
    },

    continueShopping() {
      window.location.href = '/catalogue'; // or use your route helper
    },

    // unified error handling (detects HTML responses too)
    handleRequestError(err, fallbackMessage = 'Request failed') {
      console.error(err);
      this.error = fallbackMessage;

      if (err.response) {
        const { status, data, headers } = err.response;
        // if HTML was returned (starts with '<'), show small hint
        if (typeof data === 'string' && data.trim().startsWith('<')) {
          // show short snippet in console and message to user
          console.error('Server returned HTML; open Network tab to inspect response (possible redirect to login or CSRF error).');
          this.error = `${fallbackMessage}. Server returned HTML (see console / network tab). Status: ${status}`;
          console.log(data.slice(0, 600)); // print first 600 chars
          return;
        }

        // Else if JSON error object exists, show message
        if (data && data.message) {
          this.error = data.message;
          return;
        }
        this.error = `${fallbackMessage}. Status: ${status}`;
      } else {
        this.error = err.message || fallbackMessage;
      }
    }
  }
};
</script>

<style scoped>
.cart-page .border-bottom { border-bottom: 1px solid #eee; }
</style>
