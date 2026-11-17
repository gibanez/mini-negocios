<template>
  <div class="field">
    <label v-if="label" class="label">{{ label }}</label>
    <input
      v-if="type !== 'textarea'"
      class="input"
      :type="type"
      :placeholder="placeholder"
      :value="modelValue"
      :disabled="disabled"
      v-bind="$attrs"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <textarea
      v-else
      class="input textarea"
      :placeholder="placeholder"
      :value="modelValue"
      :disabled="disabled"
      v-bind="$attrs"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <span v-if="error" class="error">{{ error }}</span>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, default: null },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  modelValue: { type: [String, Number], default: '' },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: null }
})
defineEmits(['update:modelValue'])
</script>

<style scoped>
.field { display: flex; flex-direction: column; gap: 6px; }
.label { font-size: var(--font-sm); font-weight: 600; color: var(--color-text); }
.input { min-height: 54px; }
.textarea { min-height: 100px; resize: vertical; }
.error { font-size: var(--font-sm); color: var(--color-danger); }
</style>
