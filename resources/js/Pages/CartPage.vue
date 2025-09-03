<script setup>
import axios from "axios";
import { ref, reactive, computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const auth = computed(() => page.props.auth?.user || null);

const loading = ref(true);
const cart = ref({ items: {}, count: 0, total: 0 });
const lines = ref([]);
const localQty = reactive({});
const processing = reactive({});
const processingAll = ref(false);
const error = ref(null);

const formatMoney = (v) => Number(v || 0).toFixed(2);

const initLocalQty = () => {
  Object.keys(localQty).forEach((k) => delete localQty[k]); // clear
  lines.value.forEach((line) => {
    localQty[line.product_id] = Number(line.quantity || 0);
  });
};

const loadCart = async () => {
  loading.value = true;
  error.value = null;
  try {
    const res = await axios.get("/cart/data");
    cart.value = res.data || { items: {}, count: 0, total: 0 };
    lines.value = Object.values(cart.value.items || {});
    initLocalQty();
  } catch (err) {
    handleRequestError(err, "Failed to load cart");
  } finally {
    loading.value = false;
  }
};

const updateQty = async (productId) => {
  const qty = Number(localQty[productId] ?? 0);
  if (!Number.isInteger(qty) || qty < 0) {
    return alert("Quantity must be a non-negative integer");
  }
  processing[productId] = true;
  try {
    const res = await axios.post("/cart/update", { product_id: productId, qty });
    if (res.data?.ok) {
      cart.value = res.data.cart;
      lines.value = Object.values(cart.value.items || {});
      initLocalQty();
    } else {
      throw new Error(res.data?.message || "Update failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not update item");
  } finally {
    processing[productId] = false;
  }
};

const removeItem = async (productId) => {
  if (!confirm("Remove this item from cart?")) return;
  processing[productId] = true;
  try {
    const res = await axios.post("/cart/remove", { product_id: productId });
    if (res.data?.ok) {
      cart.value = res.data.cart;
      lines.value = Object.values(cart.value.items || {});
      initLocalQty();
    } else {
      throw new Error(res.data?.message || "Remove failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not remove item");
  } finally {
    processing[productId] = false;
  }
};

const clearCart = async () => {
  if (!confirm("Clear entire cart?")) return;
  processingAll.value = true;
  try {
    const res = await axios.post("/cart/clear");
    if (res.data?.ok) {
      cart.value = res.data.cart;
      lines.value = [];
      initLocalQty();
    } else {
      throw new Error(res.data?.message || "Clear failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not clear cart");
  } finally {
    processingAll.value = false;
  }
};

const checkout = async () => {
  if (!auth.value) {
    window.location.href = "/login"; // redirect if not logged in
    return;
  }
  if (!confirm("Place order now?")) return;
  processingAll.value = true;
  try {
    const res = await axios.post("/checkout");
    if (res.data?.id) {
      alert("Order placed: #" + res.data.order_no);
      cart.value = { items: {}, count: 0, total: 0 };
      lines.value = [];
      initLocalQty();

      window.location.href = `/orders/`;
    } else {
      throw new Error(res.data?.message || "Checkout failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not place order");
  } finally {
    processingAll.value = false;
  }
};

const continueShopping = () => {
  window.location.href = "/";
};

const handleRequestError = (err, fallbackMessage) => {
  console.error(err);
  error.value = fallbackMessage;
  if (err.response) {
    const { status, data } = err.response;
    if (typeof data === "string" && data.trim().startsWith("<")) {
      error.value = `${fallbackMessage}. Server returned HTML (likely login redirect). Status: ${status}`;
      console.log(data.slice(0, 600));
      return;
    }
    if (data?.message) {
      error.value = data.message;
      return;
    }
    error.value = `${fallbackMessage}. Status: ${status}`;
  } else {
    error.value = err.message || fallbackMessage;
  }
};

loadCart();
</script>

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
            <input type="number" min="0" class="form-control" style="width:96px"
                   v-model.number="localQty[line.product_id]" />

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
              <button v-if="auth" class="btn btn-primary"
                      @click="checkout"
                      :disabled="lines.length===0 || processingAll">
                Checkout
              </button>

              <a v-else href="/login" class="btn btn-outline-primary">
                Login to checkout
              </a>
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

<style scoped>
.cart-page .border-bottom { border-bottom: 1px solid #eee; }
</style>
