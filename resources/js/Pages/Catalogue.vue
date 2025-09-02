<template>
  <div>
    <!-- Page title -->
    <Head title="Catalogue" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div v-for="p in products.data" :key="p.id" class="border p-3 rounded shadow-sm">
        <img
          v-if="p.image"
          :src="p.image"
          alt="Product Image"
          class="w-full h-36 object-cover"
        />
        <h3 class="font-medium mt-2">{{ p.name }}</h3>
        <p class="text-sm text-gray-600">{{ p.description }}</p>
        <div class="mt-1 font-semibold">RM {{ Number(p.price).toFixed(2) }}</div>
        <button
          @click="add(p.id)"
          class="mt-2 px-3 py-1 bg-blue-500 rounded hover:bg-blue-600"
        >
          Add to cart
        </button>
      </div>
    </div>

    <div class="mt-4" v-if="products.links">
      <!-- simple pagination example -->
      <nav class="flex gap-2">
        <a
          v-for="link in products.links"
          :key="link.label"
          :href="link.url || '#'"
          v-html="link.label"
          :class="[
            'px-3 py-1 border rounded',
            link.active ? 'bg-blue-500' : 'bg-white text-gray-700'
          ]"
        />
      </nav>
    </div>
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
      required: true
    }
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
    }
  }
});
</script>
