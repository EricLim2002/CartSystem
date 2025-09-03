// resources/js/stores/cart.js
import { reactive } from "vue";

export const cartStore = reactive({ count: 0 });

export function setCartCount(v) {
  cartStore.count = v ?? 0;
}

export function incrementCart(n = 1) {
  cartStore.count += n;
}
