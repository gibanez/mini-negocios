<template>
  <header class="header">
    <button v-if="back" class="back-btn" @click="goBack">
      <span class="back-icon">&#8592;</span>
    </button>
    <h1 class="title">{{ title }}</h1>
    <slot name="actions" />
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  title: { type: String, required: true },
  back: { type: String, default: null }
})

const router = useRouter()

function goBack() {
  if (props.back) {
    router.push(props.back)
  } else {
    router.back()
  }
}
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  gap: var(--gap-sm);
  padding: 14px var(--gap);
  background: var(--color-primary);
  color: #fff;
  min-height: 56px;
}
.title {
  flex: 1;
  margin: 0;
  font-size: var(--font-lg);
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.back-btn {
  color: #fff;
  font-size: 24px;
  line-height: 1;
  padding: 4px 8px;
  border-radius: var(--radius-md);
}
.back-btn:active { background: rgba(255,255,255,0.2); }
</style>
