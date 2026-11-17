<template>
  <div class="shell">
    <AppHeader :title="title" :back="back">
      <template v-if="$slots.actions" #actions>
        <slot name="actions" />
      </template>
    </AppHeader>
    <main class="shell-main">
      <slot />
    </main>
  </div>
</template>

<script setup>
import AppHeader from './AppHeader.vue'

defineProps({
  title: { type: String, default: 'Despensa' },
  back: { type: String, default: null },
})
</script>

<style scoped>
.shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--color-bg);
}
.shell-main {
  flex: 1;
  padding: var(--gap);
  padding-bottom: calc(var(--nav-height) + var(--gap));
  max-width: 600px;
  width: 100%;
  margin: 0 auto;
}
</style>
