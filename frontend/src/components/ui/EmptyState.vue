<template>
  <div class="empty">
    <div class="icon">{{ icon }}</div>
    <p class="text">{{ text }}</p>
    <slot />
  </div>
</template>

<script setup>
defineProps({
  icon: { type: String, default: '📭' },
  text: { type: String, default: 'No hay nada todavía' }
})
</script>

<style scoped>
.empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--gap);
  padding: 48px var(--gap);
  text-align: center;
}
.icon { font-size: 56px; }
.text { font-size: var(--font-lg); color: var(--color-text-light); margin: 0; }
</style>
