<script setup>
import { ref, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

// Props from Laravel (paginated result)
const props = defineProps({
  orders: Object, // paginator object with `data` array
});

// Local reactive list (copy of paginator data so we can mutate)
const ordersList = ref(props.orders?.data ? [...props.orders.data] : []);

// statuses available
const statuses = ['completed', 'cancelled'];

// localStatus holds editable status per order id so we don't mutate props directly
const localStatus = reactive({});
ordersList.value.forEach(o => {
  localStatus[o.id] = o.status;
});

/**
 * Update status on server, then update local copy
 */
const updateStatus = async (orderId) => {
  const newStatus = localStatus[orderId];
  const order = ordersList.value.find(o => o.id === orderId);

  if (!order) return;

  // Confirm with user
  const confirmed = window.confirm(
    `Are you sure you want to change Order #${order.order_no} to "${newStatus}"?\n\nThis action cannot be undone.`
  );

  if (!confirmed) {
    // rollback selection if cancelled
    localStatus[orderId] = order.status;
    return;
  }

  try {
    const response = await axios.post(`/orders/${orderId}/status`, { status: newStatus });
    order.status = response.data.status;
    alert('Order status updated!');
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || 'Failed to update status');
    // rollback on error
    localStatus[orderId] = order.status;
  }
};
</script>

<template>

  <Head title="Orders" />

  <div class="container py-5">
    <h2 class="mb-4">Orders</h2>

    <!-- show empty -->
    <div v-if="ordersList.length === 0" class="alert alert-info">
      No orders found.
    </div>

    <!-- show list -->
    <div v-else>
      <div v-for="order in ordersList" :key="order.id" class="card mb-3 shadow-sm">
        <div class="card-body">
          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title mb-0">Order #{{ order.order_no }}</h5>
            <span class="badge bg-primary text-uppercase">{{ order.status }}</span>
          </div>

          <!-- Products -->
          <ul class="list-group list-group-flush mb-3">
            <li v-for="product in order.items" :key="product.id" class="list-group-item">
              <div class="d-flex justify-content-between">
                <div>
                  <strong>{{ product.product_name }}</strong>
                  <div class="small text-muted">Qty: {{ product.pivot?.quantity ?? 1 }}</div>
                </div>
                <div class="text-end">
                  <div>Unit: {{ product.unit_price }}</div>
                  <div class="fw-bold mt-3 mb-2">
                    Subtotal:
                    {{ product.subtotal ?? (product.unit_price * (product.pivot?.quantity ?? 1)) }}
                  </div>
                </div>
              </div>
            </li>
          </ul>

          <!-- Status dropdown -->
          <div class="d-flex align-items-center gap-2" :class="{
            'd-none': order.status === 'completed' || order.status === 'cancelled'
          }">
            <label class="form-label mb-0 me-2">Change Status:</label>
            <select class="form-select w-auto" v-model="localStatus[order.id]" @change="updateStatus(order.id)">
              <option v-for="status in statuses" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-4">
        <small class="text-muted">
          Page {{ props.orders.current_page }} of {{ props.orders.last_page }}
        </small>
      </div>
    </div>
  </div>
</template>
