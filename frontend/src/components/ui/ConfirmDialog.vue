<template>
  <Teleport to="body">
    <div v-if="modelValue" class="overlay" @click.self="$emit('update:modelValue', false)">
      <div class="dialog">
        <p class="message">{{ message }}</p>
        <div class="actions">
          <BigBtn variant="ghost" @click="$emit('update:modelValue', false)">Cancelar</BigBtn>
          <BigBtn :variant="confirmVariant" @click="confirm">{{ confirmLabel }}</BigBtn>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import BigBtn from './BigBtn.vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  message: { type: String, required: true },
  confirmLabel: { type: String, default: 'Confirmar' },
  confirmVariant: { type: String, default: 'danger' }
})

const emit = defineEmits(['update:modelValue', 'confirm'])

function confirm() {
  emit('update:modelValue', false)
  emit('confirm')
}
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: flex-end;
  z-index: 100;
}
.dialog {
  width: 100%;
  background: var(--color-surface);
  border-radius: var(--radius-lg) var(--radius-lg) 0 0;
  padding: var(--gap);
  display: flex;
  flex-direction: column;
  gap: var(--gap);
}
.message { font-size: var(--font-lg); margin: 0; text-align: center; }
.actions { display: flex; gap: var(--gap); }
.actions > * { flex: 1; }
</style>
