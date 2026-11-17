<template>
  <AppShell title="Clientes">
    <template #actions>
      <router-link to="/clientes/nuevo" class="add-btn">+</router-link>
    </template>

    <BigInput v-model="search" placeholder="Buscar cliente..." type="search" @input="doSearch" />

    <LoadingSpinner v-if="loading" />
    <EmptyState v-else-if="!clients.length" icon="👤" text="No hay clientes todavía">
      <router-link to="/clientes/nuevo">
        <BigBtn>Agregar cliente</BigBtn>
      </router-link>
    </EmptyState>

    <div v-else class="client-list">
      <AppCard
        v-for="c in clients" :key="c.id"
        clickable
        class="client-row"
      >
        <div class="client-info" @click="router.push('/fiado/' + c.id)">
          <span class="client-name">{{ c.name }}</span>
          <span v-if="c.nickname" class="client-nick">"{{ c.nickname }}"</span>
          <span v-if="c.phone" class="client-phone">📞 {{ c.phone }}</span>
        </div>
        <div class="client-actions">
          <button class="edit-btn" @click="router.push('/clientes/' + c.id)">✏️</button>
          <button class="fiado-btn" @click="router.push('/fiado/' + c.id)">Fiado →</button>
        </div>
      </AppCard>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getClients } from '@/api/clients.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import AppCard from '@/components/ui/AppCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const router = useRouter()
const clients = ref([])
const search = ref('')
const loading = ref(false)

let debounce = null
function doSearch() {
  clearTimeout(debounce)
  debounce = setTimeout(loadClients, 300)
}

async function loadClients() {
  loading.value = true
  try {
    const data = await getClients(search.value)
    const list = Array.isArray(data) ? data : (data.clients || [])
    clients.value = list.filter(c => c.status === 'active')
  } finally {
    loading.value = false
  }
}

onMounted(loadClients)
</script>

<style scoped>
.add-btn { color: #fff; font-size: 28px; font-weight: bold; padding: 0 8px; }
.client-list { display: flex; flex-direction: column; gap: var(--gap-sm); margin-top: var(--gap); }
.client-row { display: flex; justify-content: space-between; align-items: center; gap: var(--gap); }
.client-info { flex: 1; display: flex; flex-direction: column; gap: 2px; cursor: pointer; }
.client-name { font-size: var(--font-md); font-weight: 700; }
.client-nick { font-size: var(--font-sm); color: var(--color-text-light); font-style: italic; }
.client-phone { font-size: var(--font-sm); color: var(--color-text-light); }
.client-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.edit-btn { font-size: 20px; padding: 6px; }
.fiado-btn { font-size: var(--font-sm); color: var(--color-primary); font-weight: 700; padding: 8px 12px; border: 2px solid var(--color-primary); border-radius: var(--radius-md); }
</style>
