<script setup>
import axios from "axios";
import { ref, reactive, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import AlertBox from "@/Components/AlertBox.vue";

// only logic change: import setCartCount so header badge stays in sync
import { setCartCount } from "@/app";

const page = usePage();
const auth = computed(() => page.props.auth?.user || null);

const loading = ref(true);
const cart = ref({ items: {}, count: 0, total: 0 });
const lines = ref([]);
const localQty = reactive({});
const processing = reactive({});
const processingAll = ref(false);
const error = ref(null);

const alertRef = ref(null);

// Modal state
const modalAction = ref(null);   // "remove" | "clear" | "checkout"
const selectedProductId = ref(null);
const showModal = ref(false);

const formatMoney = (v) => Number(v || 0).toFixed(2);

const initLocalQty = () => {
  Object.keys(localQty).forEach((k) => delete localQty[k]);
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

    // sync global header/cart count (no UI change)
    setCartCount(cart.value.count ?? 0);
  } catch (err) {
    handleRequestError(err, "Failed to load cart");
  } finally {
    loading.value = false;
  }
};

const updateQty = async (productId) => {
  const qty = Number(localQty[productId] ?? 0);
  if (!Number.isInteger(qty) || qty < 0) {
    return showAlert("Quantity must be a non-negative integer", "warning");
  }
  processing[productId] = true;
  try {
    const res = await axios.post("/cart/update", { product_id: productId, qty });
    if (res.data?.ok) {
      cart.value = res.data.cart;
      lines.value = Object.values(cart.value.items || {});
      initLocalQty();

      // sync global header/cart count
      setCartCount(cart.value.count ?? 0);

      showAlert("Quantity updated successfully", "success");
    } else {
      throw new Error(res.data?.message || "Update failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not update item");
  } finally {
    processing[productId] = false;
  }
};

const removeItem = async () => {
  const productId = selectedProductId.value;
  processing[productId] = true;
  try {
    const res = await axios.post("/cart/remove", { product_id: productId });
    if (res.data?.ok) {
      cart.value = res.data.cart;
      lines.value = Object.values(cart.value.items || {});
      initLocalQty();

      // sync global header/cart count
      setCartCount(cart.value.count ?? 0);

      showAlert("Item removed from cart", "success");
    } else {
      throw new Error(res.data?.message || "Remove failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not remove item");
  } finally {
    processing[productId] = false;
    closeModal();
  }
};

const clearCart = async () => {
  processingAll.value = true;
  try {
    const res = await axios.post("/cart/clear");
    if (res.data?.ok) {
      cart.value = res.data.cart;
      lines.value = [];
      initLocalQty();

      // sync global header/cart count
      setCartCount(cart.value.count ?? 0);

      showAlert("Cart cleared", "success");
    } else {
      throw new Error(res.data?.message || "Clear failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not clear cart");
  } finally {
    processingAll.value = false;
    closeModal();
  }
};

const checkout = async () => {
  if (!auth.value) {
    window.location.href = "/login";
    return;
  }
  processingAll.value = true;
  try {
    const res = await axios.post("/checkout");
    if (res.data?.id) {
      showAlert("Order placed successfully!", "success");

      // clear local cart and sync global count to zero
      cart.value = { items: {}, count: 0, total: 0 };
      lines.value = [];
      initLocalQty();
      setCartCount(0);

      // redirect to orders page
      window.location.href = `/orders/`;
    } else {
      throw new Error(res.data?.message || "Checkout failed");
    }
  } catch (err) {
    handleRequestError(err, "Could not place order");
  } finally {
    processingAll.value = false;
    closeModal();
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
    if (data?.message) {
      error.value = data.message;
    } else {
      error.value = `${fallbackMessage}. Status: ${status}`;
    }
  } else {
    error.value = err.message || fallbackMessage;
  }
  showAlert(error.value, "danger");
};

function showAlert(msg, variant = "info") {
  alertRef.value?.show(msg, variant);
}

// Modal helpers
function openModal(action, productId = null) {
  modalAction.value = action;
  selectedProductId.value = productId;
  showModal.value = true;
}
function closeModal() {
  modalAction.value = null;
  selectedProductId.value = null;
  showModal.value = false;
}

// initial load
loadCart();
</script>


<template>
  <div class="cart-page container py-5">
    <h1 class="mb-4 fw-bold">🛒 Your Cart</h1>

    <!-- Loading -->
    <div v-if="loading" class="text-center text-muted my-5">
      <div class="spinner-border text-primary mb-2"></div>
      <div>Loading your cart…</div>
    </div>

    <!-- Cart content -->
    <div v-else>
      <div v-if="lines.length === 0" class="alert alert-info text-center py-4 shadow-sm">
        <strong>Your cart is empty.</strong>  
        <div class="mt-2">
          <button class="btn btn-primary btn-sm" @click="continueShopping">Start Shopping</button>
        </div>
      </div>

      <div v-else class="row g-3">
        <!-- Cart items -->
        <div class="col-lg-8">
          <div v-for="line in lines" :key="line.product_id" class="card shadow-sm border-0 mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h6 class="fw-semibold mb-1">{{ line.name }}</h6>
                <div class="small text-muted">ID: {{ line.product_id }}</div>
                <div class="small text-muted">Unit: RM {{ formatMoney(line.unit_price) }}</div>
              </div>

              <div class="d-flex align-items-center gap-2">
                <input type="number" min="0" class="form-control form-control-sm text-center"
                       style="width:80px" v-model.number="localQty[line.product_id]" />

                <button class="btn btn-sm btn-outline-secondary"
                        :disabled="processing[line.product_id]"
                        @click="updateQty(line.product_id)">
                  <span v-if="processing[line.product_id]" class="spinner-border spinner-border-sm"></span>
                  <span v-else>Update</span>
                </button>

                <div class="ms-3 fw-semibold">RM {{ formatMoney(line.subtotal) }}</div>

                <button class="btn btn-sm btn-outline-danger ms-3"
                        :disabled="processing[line.product_id]"
                        @click="openModal('remove', line.product_id)">
                  <i class="bi bi-trash"></i> Remove
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart summary -->
        <div class="col-lg-4">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="fw-bold mb-3">Summary</h5>
              <div class="d-flex justify-content-between small mb-1">
                <span>Items</span>
                <span>{{ cart.count }}</span>
              </div>
              <div class="d-flex justify-content-between fs-5 fw-semibold border-top pt-2">
                <span>Total</span>
                <span>RM {{ formatMoney(cart.total) }}</span>
              </div>

              <div class="mt-4 d-grid gap-2">
                <button v-if="auth"
                        class="btn btn-primary btn-lg"
                        @click="openModal('checkout')"
                        :disabled="lines.length===0 || processingAll">
                  Checkout
                </button>

                <a v-else href="/login" class="btn btn-outline-primary btn-lg">
                  Login to Checkout
                </a>

                <button class="btn btn-outline-danger"
                        @click="openModal('clear')"
                        :disabled="processingAll">
                  Clear Cart
                </button>

                <button class="btn btn-light" @click="continueShopping">
                  Continue Shopping
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- AlertBox -->
    <AlertBox ref="alertRef" v-if="error" :message="error" variant="danger" class="mt-4 shadow-sm" />

    <!-- Bootstrap Modal -->
    <div class="modal fade" :class="{ show: showModal }" style="display: block;" v-if="showModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <span v-if="modalAction==='remove'">Remove Item</span>
              <span v-else-if="modalAction==='clear'">Clear Cart</span>
              <span v-else-if="modalAction==='checkout'">Confirm Checkout</span>
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <p v-if="modalAction==='remove'">Are you sure you want to remove this item?</p>
            <p v-else-if="modalAction==='clear'">Are you sure you want to clear your entire cart?</p>
            <p v-else-if="modalAction==='checkout'">Are you sure you want to place this order?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button v-if="modalAction==='remove'" class="btn btn-danger" @click="removeItem">Remove</button>
            <button v-if="modalAction==='clear'" class="btn btn-danger" @click="clearCart">Clear</button>
            <button v-if="modalAction==='checkout'" class="btn btn-primary" @click="checkout">Checkout</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-page h1 {
  font-size: 1.75rem;
}

.card {
  transition: all 0.2s ease-in-out;
  border-radius: 0.75rem;
}
.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1);
}

.btn {
  transition: all 0.2s ease-in-out;
}
.btn:hover {
  transform: translateY(-1px);
}

input[type="number"] {
  border-radius: 0.5rem;
}

/* Modal fix for manual show */
.modal.show {
  display: block;
  background: rgba(0,0,0,.5);
}
</style>
