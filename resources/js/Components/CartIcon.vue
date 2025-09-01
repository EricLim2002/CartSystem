<template>
  <div>
    <a href="/cart">Cart <span>{{ count }}</span></a>
  </div>
</template>

<script>
export default {
  data() { return { count: 0 }; },
  mounted() {
    // initial fetch
    axios.get('/cart/count').then(r => this.count = r.data.count).catch(()=>{});
    // optionally listen to events via window
    window.addEventListener('cart:updated', (e) => { this.count = e.detail.count; });
  }
}
</script>
