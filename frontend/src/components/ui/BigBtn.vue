<template>
  <button
    class="big-btn"
    :class="[`variant-${variant}`, { loading, block }]"
    :disabled="disabled || loading"
    v-bind="$attrs"
  >
    <span v-if="loading" class="spinner" />
    <slot />
  </button>
</template>

<script setup>
defineProps({
  variant: { type: String, default: 'primary' },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  block: { type: Boolean, default: true }
})
</script>

<style scoped>
.big-btn {
  min-height: 56px;
  padding: 12px 20px;
  border-radius: var(--radius-lg);
  font-size: var(--font-md);
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: opacity 0.15s, transform 0.1s;
  cursor: pointer;
  border: none;
}
.big-btn.block { width: 100%; }
.big-btn:active:not(:disabled) { transform: scale(0.98); }
.big-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.variant-primary {
  background: var(--color-primary);
  color: #fff;
}
.variant-secondary {
  background: var(--color-primary-light);
  color: var(--color-primary);
}
.variant-danger {
  background: var(--color-danger);
  color: #fff;
}
.variant-ghost {
  background: transparent;
  color: var(--color-primary);
  border: 2px solid var(--color-primary);
}

.spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255,255,255,0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
