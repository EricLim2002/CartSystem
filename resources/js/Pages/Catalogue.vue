<template>
    <div class="min-vh-100 container py-1">
      <!-- Page title -->

      <Head title="Catalogue" />

      <!-- Product grid -->
      <div class="row py-2 col-12">
        <div v-for="p in products.data" :key="p.id" class="col-md-4 mb-2">
          <div class="card h-100 shadow-sm">
            <img v-if="p.image" :src="p.image" alt="Product Image" class="card-img-top"
              style="height: 9rem; object-fit: cover;" />
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
    </div>
    <div class="fixed bottom-0 left-0 w-full bg-white shadow-md container" v-if="products.links">
      <nav>
        <ul class="pagination">
          <li v-for="link in products.links" :key="link.label" :class="['page-item', { active: link.active }]">
            <a class="page-link" :href="link.url || '#'" v-html="link.label"></a>
          </li>
        </ul>
      </nav>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";

export default defineComponent({
  components: { Head },
  props: {
    products: {
      type: Object,
      required: true,
    },
  },
  methods: {
    async add(id) {
      try {
        await axios.post("/cart/add", { product_id: id, qty: 1 });
        alert("Added to cart");
      } catch (e) {
        console.error(e);
        alert("Failed to add to cart");
      }
    },
  },
});
</script>
