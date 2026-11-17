<template>
  <AppShell title="Catálogo">
    <template #actions>
      <router-link to="/catalogo/producto/nuevo" class="add-btn">+</router-link>
    </template>

    <div class="search-bar">
      <BigInput v-model="search" placeholder="Buscar producto..." type="search" />
    </div>

    <div v-if="!loading && catalog.length" class="cat-chips">
      <button
        class="chip"
        :class="{ active: !filterCat }"
        @click="filterCat = null"
      >Todas</button>
      <button
        v-for="cat in activeCatalog"
        :key="cat.id"
        class="chip"
        :class="{ active: filterCat === cat.id }"
        @click="filterCat = filterCat === cat.id ? null : cat.id"
      >{{ cat.name }}</button>
    </div>

    <LoadingSpinner v-if="loading" />

    <div v-else-if="!catalog.length" class="empty-wrap">
      <EmptyState icon="📦" text="No hay productos todavía">
        <router-link to="/catalogo/producto/nuevo">
          <BigBtn>Agregar producto</BigBtn>
        </router-link>
      </EmptyState>
    </div>

    <div v-else class="catalog">
      <!-- Sin búsqueda: agrupado por categoría -->
      <template v-if="!search.trim()">
        <div v-for="cat in visibleCatalog" :key="cat.id" class="cat-section">
          <div class="cat-header">
            <span class="cat-name">{{ cat.name }}</span>
            <span class="cat-count">{{ cat.products.filter(p => p.status === 'active').length }}</span>
          </div>
          <div class="product-list">
            <AppCard
              v-for="p in cat.products.filter(p => p.status === 'active')"
              :key="p.id"
              clickable
              class="product-row"
              @click="router.push('/catalogo/producto/' + p.id)"
            >
              <div class="prod-info">
                <span class="prod-name">{{ p.name }}</span>
                <span class="prod-unit">{{ p.unit }}</span>
              </div>
              <div class="prod-right">
                <span class="prod-price" :class="{ 'no-price': p.price == null }">
                  {{ p.price != null ? '$' + formatPrice(p.price) : 'Sin precio' }}
                </span>
                <span class="prod-stock" :class="stockClass(p.stock)">
                  Stock: {{ formatStock(p.stock) }}
                </span>
              </div>
            </AppCard>
          </div>
        </div>
      </template>

      <!-- Con búsqueda: lista plana filtrada -->
      <template v-else>
        <div v-if="searchResults.length" class="product-list">
          <AppCard
            v-for="p in searchResults"
            :key="p.id"
            clickable
            class="product-row"
            @click="router.push('/catalogo/producto/' + p.id)"
          >
            <div class="prod-info">
              <span class="prod-name">{{ p.name }}</span>
              <span class="prod-unit">{{ p.catName }} · {{ p.unit }}</span>
            </div>
            <div class="prod-right">
              <span class="prod-price" :class="{ 'no-price': p.price == null }">
                {{ p.price != null ? '$' + formatPrice(p.price) : 'Sin precio' }}
              </span>
              <span class="prod-stock" :class="stockClass(p.stock)">
                Stock: {{ formatStock(p.stock) }}
              </span>
            </div>
          </AppCard>
        </div>
        <EmptyState v-else icon="🔍" text="Sin resultados para tu búsqueda." />
      </template>
    </div>

    <router-link to="/catalogo/categoria/nueva" class="cat-link">+ Nueva categoría</router-link>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getCatalog } from '@/api/catalog.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import AppCard from '@/components/ui/AppCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const router    = useRouter()
const catalog   = ref([])
const loading   = ref(true)
const search    = ref('')
const filterCat = ref(null)

const activeCatalog = computed(() =>
  catalog.value.filter(cat =>
    cat.status === 'active' &&
    cat.products.some(p => p.status === 'active')
  )
)

const visibleCatalog = computed(() => {
  if (!filterCat.value) return activeCatalog.value
  return activeCatalog.value.filter(c => c.id === filterCat.value)
})

const searchResults = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return []
  const results = []
  for (const cat of catalog.value) {
    if (cat.status !== 'active') continue
    for (const p of cat.products) {
      if (p.status !== 'active') continue
      if (p.name.toLowerCase().includes(q) || (p.brand || '').toLowerCase().includes(q)) {
        results.push({ ...p, catName: cat.name })
      }
    }
  }
  return results
})

function formatPrice(cents) {
  if (cents == null) return '—'
  return (cents / 100).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 })
}

function formatStock(millis) {
  if (millis == null) return '0'
  const val = millis / 1000
  return Number.isInteger(val) ? val.toString() : val.toFixed(2)
}

function stockClass(millis) {
  if (!millis || millis <= 0) return 'stock-out'
  if (millis < 5000) return 'stock-low'
  return 'stock-ok'
}

onMounted(async () => {
  try {
    catalog.value = await getCatalog()
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.search-bar { margin-bottom: 10px; }

.cat-chips {
  display: flex; gap: 8px; overflow-x: auto; padding-bottom: 10px;
  scrollbar-width: none;
}
.cat-chips::-webkit-scrollbar { display: none; }
.chip {
  flex-shrink: 0;
  padding: 6px 14px; border-radius: 999px;
  border: 1.5px solid var(--color-border);
  background: transparent; color: var(--color-text-light);
  font-size: var(--font-sm); font-weight: 600; cursor: pointer;
  transition: all 0.15s;
}
.chip.active {
  background: var(--color-primary); border-color: var(--color-primary);
  color: #fff;
}
.empty-wrap { padding: var(--gap) 0; }
.catalog { display: flex; flex-direction: column; gap: 24px; }

.cat-section { display: flex; flex-direction: column; gap: 8px; }
.cat-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 6px 4px; border-bottom: 2px solid var(--color-primary);
}
.cat-name { font-size: var(--font-md); font-weight: 700; color: var(--color-text); }
.cat-count {
  background: var(--color-primary); color: #fff;
  font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 999px;
}

.product-list { display: flex; flex-direction: column; gap: 6px; }

.product-row {
  display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.prod-info { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 0; }
.prod-name {
  font-size: var(--font-base); font-weight: 600; color: var(--color-text);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.prod-unit { font-size: var(--font-sm); color: var(--color-text-light); }

.prod-right { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; flex-shrink: 0; }
.prod-price { font-size: var(--font-base); font-weight: 700; color: var(--color-primary); }
.prod-price.no-price { color: var(--color-text-light); font-weight: 400; font-style: italic; font-size: var(--font-sm); }
.prod-stock { font-size: var(--font-sm); }
.stock-ok  { color: var(--color-success, #2e7d32); }
.stock-low { color: var(--color-warning, #f59e0b); font-weight: 600; }
.stock-out { color: var(--color-danger); font-weight: 600; }

.add-btn {
  background: rgba(255,255,255,0.2); color: #fff;
  border-radius: 50%; width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; font-weight: 300; text-decoration: none; line-height: 1;
}

.cat-link {
  display: block; text-align: center; color: var(--color-primary);
  font-size: var(--font-sm); font-weight: 600; padding: var(--gap) 0;
  text-decoration: none;
}
</style>
