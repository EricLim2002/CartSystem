<template>
  <div class="min-vh-100 container py-4">
    <!-- Page title -->
    <h1 class="fw-bold mb-4">🛍️ Products</h1>

    <!-- Product grid -->
    <div class="row g-3">
      <div v-for="p in products.data" :key="p.id" class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm product-card">
          <img
            v-if="p.image"
            :src="p.image"
            alt="Product Image"
            class="card-img-top"
            style="height: 9rem; object-fit: cover;"
          />
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ p.name }}</h5>
            <p class="card-text text-muted small">{{ p.description }}</p>
            <div class="mt-auto fw-semibold">RM {{ Number(p.price).toFixed(2) }}</div>
            <button @click="add(p.id)" class="btn btn-primary btn-sm mt-2">
              Add to cart
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4" v-if="products.links">
      <nav>
        <ul class="pagination">
          <li
            v-for="link in products.links"
            :key="link.label"
            :class="['page-item', { active: link.active, disabled: !link.url }]"
          >
            <a
              class="page-link"
              :href="link.url || '#'"
              v-html="link.label"
            ></a>
          </li>
        </ul>
      </nav>
    </div>

    <!-- AlertBox -->
    <AlertBox ref="alertRef" />
  </div>
</template>

<script>
import { defineComponent, ref } from "vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import AlertBox from "@/Components/AlertBox.vue";

export default defineComponent({
  components: { Head, AlertBox },
  props: {
    products: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const alertRef = ref(null);

    const showAlert = (msg, variant = "info") => {
      alertRef.value?.show(msg, variant);
    };

    const add = async (id) => {
      try {
        await axios.post("/cart/add", { product_id: id, qty: 1 });
        showAlert("✅ Added to cart", "success");
      } catch (e) {
        console.error(e);
        showAlert("❌ Failed to add to cart", "danger");
      }
    };

    return { add, alertRef };
  },
});
</script>

<style scoped>
.product-card {
  border-radius: 0.75rem;
  transition: all 0.2s ease-in-out;
}
.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.75rem 1.25rem rgba(0, 0, 0, 0.1);
}
.page-link {
  cursor: pointer;
}
</style>
