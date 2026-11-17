<template>
  <nav class="bottom-nav">
    <router-link v-for="tab in tabs" :key="tab.to" :to="tab.to" class="tab" :class="{ active: isActive(tab) }">
      <span class="tab-icon" v-html="tab.icon" />
      <span class="tab-label">{{ tab.label }}</span>
    </router-link>
  </nav>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()

const tabs = [
  { to: '/ventas',   label: 'Ventas',    icon: '&#128203;', prefix: ['/ventas', '/vender'] },
  { to: '/catalogo', label: 'Productos', icon: '&#128722;', prefix: '/catalogo' },
  { to: '/home',     label: 'Inicio',    icon: '&#127968;', prefix: '/home' },
  { to: '/clientes', label: 'Clientes',  icon: '&#129309;', prefix: ['/clientes', '/fiado'] },
  { to: '/config',   label: 'Config',    icon: '&#9881;',   prefix: '/config' },
]

function isActive(tab) {
  const prefixes = Array.isArray(tab.prefix) ? tab.prefix : [tab.prefix]
  return prefixes.some(p => route.path.startsWith(p))
}
</script>

<style scoped>
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: var(--nav-height);
  background: var(--color-surface);
  border-top: 2px solid var(--color-border);
  display: flex;
  z-index: 10;
}
.tab {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  color: var(--color-text-light);
  text-decoration: none;
  transition: color 0.15s;
  padding: 6px 2px;
}
.tab.active { color: var(--color-primary); }
.tab-icon { font-size: 22px; line-height: 1; }
.tab-label { font-size: 11px; font-weight: 500; }
</style>
