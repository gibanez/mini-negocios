<template>
  <div class="loading-wrap">
    <div class="spinner" />
  </div>
</template>

<style scoped>
.loading-wrap {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 48px;
}
.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--color-border);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
