<template>
  <div class="card" :class="{ clickable }" v-bind="$attrs">
    <slot />
  </div>
</template>

<script setup>
defineProps({ clickable: { type: Boolean, default: false } })
</script>

<style scoped>
.card {
  background: var(--color-surface);
  border-radius: var(--radius-lg);
  padding: var(--gap);
  border: 1px solid var(--color-border);
}
.card.clickable { cursor: pointer; transition: box-shadow 0.15s; }
.card.clickable:active { box-shadow: 0 0 0 3px var(--color-primary-light); }
</style>
