<template>
  <div class="theme-picker">
    <button
      v-for="t in themes"
      :key="t.id"
      class="theme-card"
      :class="{ selected: modelValue === t.id }"
      :style="{ background: t.bg }"
      @click="$emit('update:modelValue', t.id)"
    >
      <div class="theme-preview">
        <div class="preview-header" :style="{ background: t.color }">
          <div class="preview-dot" />
          <div class="preview-bar" />
        </div>
        <div class="preview-body" :style="{ background: t.bg }">
          <div class="preview-card" :style="{ background: t.surface, borderColor: t.border }">
            <div class="preview-line" :style="{ background: t.color }" />
            <div class="preview-line short" :style="{ background: t.textLight }" />
          </div>
        </div>
      </div>
      <div class="theme-footer" :style="{ background: t.surface, borderColor: t.border }">
        <span class="theme-label" :style="{ color: t.text }">{{ t.label }}</span>
        <span v-if="modelValue === t.id" class="theme-check" :style="{ color: t.color }">✓</span>
      </div>
    </button>
  </div>
</template>

<script setup>
defineProps({ modelValue: { type: String, default: 'verde' } })
defineEmits(['update:modelValue'])

const themes = [
  { id: 'verde',      label: 'Verde',      color: '#2e7d32', bg: '#f1f8e9', surface: '#fff', border: '#c8e6c9', text: '#1a1a1a', textLight: '#888' },
  { id: 'clasico',    label: 'Clásico',    color: '#37474f', bg: '#f5f5f5', surface: '#fff', border: '#cfd8dc', text: '#212121', textLight: '#888' },
  { id: 'pizarra',    label: 'Pizarra',    color: '#455a64', bg: '#eceff1', surface: '#fff', border: '#b0bec5', text: '#263238', textLight: '#888' },
  { id: 'zafiro',     label: 'Zafiro',     color: '#1565c0', bg: '#e8eaf6', surface: '#fff', border: '#bbdefb', text: '#1a1a1a', textLight: '#888' },
  { id: 'vino',       label: 'Vino',       color: '#880e4f', bg: '#fce4ec', surface: '#fff', border: '#f8bbd9', text: '#1a1a1a', textLight: '#888' },
  { id: 'medianoche', label: 'Noche',      color: '#7c4dff', bg: '#12121f', surface: '#1e1e2e', border: '#3a3a5a', text: '#e8e8f0', textLight: '#aaa' },
  { id: 'coral',      label: 'Coral',      color: '#e64a19', bg: '#fff3e0', surface: '#fff', border: '#ffccbc', text: '#1a1a1a', textLight: '#888' },
  { id: 'aguamarina', label: 'Aguamarina', color: '#00695c', bg: '#e0f2f1', surface: '#fff', border: '#b2dfdb', text: '#1a1a1a', textLight: '#888' },
  { id: 'cielo',      label: 'Cielo',      color: '#0277bd', bg: '#e1f5fe', surface: '#fff', border: '#b3e5fc', text: '#1a1a1a', textLight: '#888' },
  { id: 'ambar',      label: 'Ámbar',      color: '#f57f00', bg: '#fffde7', surface: '#fff', border: '#ffe082', text: '#1a1a1a', textLight: '#888' },
  { id: 'rosa',       label: 'Rosa',       color: '#d81b60', bg: '#fce4ec', surface: '#fff', border: '#f48fb1', text: '#1a1a1a', textLight: '#888' },
  { id: 'carbon',     label: 'Carbón',     color: '#e0e0e0', bg: '#121212', surface: '#1e1e1e', border: '#333', text: '#f5f5f5', textLight: '#aaa' },
]
</script>

<style scoped>
.theme-picker {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}
.theme-card {
  border-radius: var(--radius-md);
  overflow: hidden;
  border: 3px solid transparent;
  cursor: pointer;
  padding: 0;
  transition: border-color 0.15s, transform 0.1s;
}
.theme-card:active { transform: scale(0.97); }
.theme-card.selected { border-color: var(--color-text); }
.theme-preview { display: flex; flex-direction: column; }
.preview-header {
  height: 28px;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 8px;
}
.preview-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.6); }
.preview-bar { flex: 1; height: 6px; border-radius: 3px; background: rgba(255,255,255,0.4); }
.preview-body { padding: 6px; }
.preview-card {
  border-radius: 4px;
  border: 1px solid;
  padding: 5px 6px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.preview-line { height: 5px; border-radius: 3px; }
.preview-line.short { width: 60%; }
.theme-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 5px 8px;
  border-top: 1px solid;
}
.theme-label { font-size: 11px; font-weight: 700; }
.theme-check { font-size: 14px; font-weight: 800; }
</style>
