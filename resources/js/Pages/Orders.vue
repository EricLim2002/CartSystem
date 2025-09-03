<script setup>
import { ref, reactive } from "vue";
import { Head } from "@inertiajs/vue3";
import axios from "axios";
import AlertBox from "@/Components/AlertBox.vue"; // ✅ use your alert component

// Props from Laravel (paginated result)
const props = defineProps({
  orders: Object, // paginator object with `data` array
});

// Local reactive list (copy of paginator data so we can mutate)
const ordersList = ref(props.orders?.data ? [...props.orders.data] : []);

// statuses available
const statuses = ["completed", "cancelled"];

// localStatus holds editable status per order id so we don't mutate props directly
const localStatus = reactive({});
ordersList.value.forEach((o) => {
  localStatus[o.id] = o.status;
});

// alert state
const alert = reactive({
  type: "",
  message: "",
  show: false,
});

const showAlert = (type, message) => {
  alert.type = type;
  alert.message = message;
  alert.show = true;
  setTimeout(() => (alert.show = false), 3000);
};

/**
 * Update status on server, then update local copy
 */
const updateStatus = async (orderId) => {
  const newStatus = localStatus[orderId];
  const order = ordersList.value.find((o) => o.id === orderId);

  if (!order) return;

  // Confirm with user
  const confirmed = window.confirm(
    `Change Order #${order.order_no} to "${newStatus}"?\nThis action cannot be undone.`
  );

  if (!confirmed) {
    // rollback selection if cancelled
    localStatus[orderId] = order.status;
    return;
  }

  try {
    const response = await axios.post(`/orders/${orderId}/status`, {
      status: newStatus,
    });
    order.status = response.data.status;
    showAlert("success", `Order #${order.order_no} updated to "${order.status}".`);
  } catch (error) {
    console.error(error);
    showAlert("danger", error.response?.data?.message || "Failed to update status.");
    // rollback on error
    localStatus[orderId] = order.status;
  }
};
</script>

<template>
  <div class="container py-5">
    <Head title="Orders" />
    <h2 class="mb-4 fw-bold">Orders</h2>

    <!-- Alert -->
    <AlertBox v-if="alert.show" :type="alert.type" :message="alert.message" />

    <!-- show empty -->
    <div v-if="ordersList.length === 0" class="alert alert-info">
      No orders found.
    </div>

    <!-- show list -->
    <div v-else>
      <div
        v-for="order in ordersList"
        :key="order.id"
        class="card mb-4 shadow-sm border-0"
      >
        <div class="card-body">
          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0 fw-semibold">
              Order #{{ order.order_no }}
            </h5>
            <span
              class="badge text-uppercase px-3 py-2"
              :class="{
                'bg-primary': order.status === 'pending',
                'bg-success': order.status === 'completed',
                'bg-danger': order.status === 'cancelled'
              }"
            >
              {{ order.status }}
            </span>
          </div>

          <!-- Products Table -->
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead class="table-light">
                <tr>
                  <th>Product</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Unit Price</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in order.items" :key="product.id">
                  <td>
                    <strong>{{ product.product_name }}</strong>
                  </td>
                  <td class="text-center">
                    {{ product.pivot?.quantity ?? 1 }}
                  </td>
                  <td class="text-end">
                    RM {{ Number(product.unit_price).toFixed(2) }}
                  </td>
                  <td class="text-end fw-bold">
                    RM
                    {{
                      product.subtotal ??
                      (product.unit_price * (product.pivot?.quantity ?? 1)).toFixed(2)
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Status dropdown -->
          <div
            class="d-flex align-items-center gap-2 mt-3"
            v-if="order.status !== 'completed' && order.status !== 'cancelled'"
          >
            <label class="form-label mb-0 me-2 fw-semibold">Change Status:</label>
            <select
              class="form-select w-auto"
              v-model="localStatus[order.id]"
              @change="updateStatus(order.id)"
            >
              <option v-for="status in statuses" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-4 text-center">
        <small class="text-muted">
          Page {{ props.orders.current_page }} of {{ props.orders.last_page }}
        </small>
      </div>
    </div>
  </div>
</template>
