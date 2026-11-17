<template>
  <div class="onboarding">
    <div class="steps-indicator">
      <div v-for="i in 3" :key="i" class="step-dot" :class="{ active: step >= i, done: step > i }" />
    </div>

    <!-- Step 1: Nombre del comercio y tema -->
    <div v-if="step === 1" class="step-content">
      <div class="icon">🏪</div>
      <h2>Personalizá tu app</h2>
      <h3 class="section-title">Elegí tu color</h3>
      <ThemePicker v-model="selectedTheme" />
      <BigBtn @click="nextStep">Siguiente</BigBtn>
    </div>

    <!-- Step 2: Rubro -->
    <div v-if="step === 2" class="step-content">
      <div class="icon">📦</div>
      <h2>¿Qué tipo de negocio tenés?</h2>
      <p class="hint">Te cargamos un catálogo de inicio según el rubro</p>
      <LoadingSpinner v-if="loadingIndustries" />
      <div v-else class="industries-list">
        <AppCard
          v-for="ind in industries" :key="ind.id"
          clickable
          class="industry-card"
          :class="{ selected: selectedIndustry === ind.id }"
          @click="selectedIndustry = ind.id"
        >
          <span class="ind-icon">{{ ind.icon || '🏪' }}</span>
          <span class="ind-name">{{ ind.name }}</span>
          <span v-if="selectedIndustry === ind.id" class="ind-check">✓</span>
        </AppCard>
      </div>
      <div class="nav-btns">
        <BigBtn variant="ghost" @click="step = 1">Atrás</BigBtn>
        <BigBtn @click="nextStep" :disabled="!selectedIndustry">Siguiente</BigBtn>
      </div>
    </div>

    <!-- Step 3: Confirmar -->
    <div v-if="step === 3" class="step-content">
      <div class="icon">🎉</div>
      <h2>¡Todo listo!</h2>
      <AppCard class="summary-card">
        <p><strong>Negocio:</strong> {{ storeName }}</p>
        <p><strong>Rubro:</strong> {{ selectedIndustryName }}</p>
        <p><strong>Color:</strong> {{ selectedThemeLabel }}</p>
      </AppCard>
      <p class="hint">Podés cambiar todo esto más adelante desde Configuración.</p>
      <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
      <div class="nav-btns">
        <BigBtn variant="ghost" @click="step = 2">Atrás</BigBtn>
        <BigBtn @click="finish" :loading="loading">¡Empezar!</BigBtn>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { getIndustries, completeOnboarding, chooseIndustry } from '@/api/onboarding.js'
import { api } from '@/api/client.js'
import BigInput from '@/components/ui/BigInput.vue'
import ThemePicker from '@/components/ui/ThemePicker.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import AppCard from '@/components/ui/AppCard.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const router = useRouter()
const auth = useAuthStore()

const step = ref(1)
const storeName = ref('')
const selectedTheme = ref('verde')
const selectedIndustry = ref(null)
const industries = ref([])
const loadingIndustries = ref(false)
const loading = ref(false)
const errors = ref({})


const selectedIndustryName = computed(() => {
  const ind = industries.value.find(i => i.id === selectedIndustry.value)
  return ind ? ind.name : ''
})

const selectedThemeLabel = computed(() => selectedTheme.value)

onMounted(async () => {
  loadingIndustries.value = true
  try {
    const [onbData, indData] = await Promise.all([
      api.get('/onboarding'),
      getIndustries()
    ])
    if (onbData?.name) storeName.value = onbData.name
    if (onbData?.theme) selectedTheme.value = onbData.theme
    industries.value = Array.isArray(indData) ? indData : (indData.industries || [])
  } catch (e) {
    // non-fatal
  } finally {
    loadingIndustries.value = false
  }
})

function nextStep() {
  errors.value = {}
  if (step.value === 1) auth.setTheme(selectedTheme.value)
  step.value++
}

async function finish() {
  loading.value = true
  errors.value = {}
  try {
    await completeOnboarding({ store_name: storeName.value, theme: selectedTheme.value })
    if (selectedIndustry.value) {
      await chooseIndustry(selectedIndustry.value)
    }
    auth.setOnboardingCompleted(true)
    router.push('/')
  } catch (e) {
    errors.value.general = e.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.onboarding {
  min-height: 100vh;
  padding: var(--gap);
  display: flex;
  flex-direction: column;
  gap: var(--gap);
  background: var(--color-bg);
  max-width: 500px;
  margin: 0 auto;
}
.steps-indicator {
  display: flex;
  gap: 8px;
  justify-content: center;
  padding: var(--gap) 0;
}
.step-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--color-border);
  transition: background 0.3s;
}
.step-dot.active, .step-dot.done { background: var(--color-primary); }
.step-content {
  display: flex;
  flex-direction: column;
  gap: var(--gap);
}
.icon { font-size: 56px; text-align: center; }
h2 { font-size: var(--font-xl); margin: 0; text-align: center; }
h3 { font-size: var(--font-md); margin: 0; }
.section-title { margin-top: 4px; }
.hint { color: var(--color-text-light); font-size: var(--font-sm); margin: 0; text-align: center; }

.industries-list { display: flex; flex-direction: column; gap: var(--gap-sm); }
.industry-card {
  display: flex;
  align-items: center;
  gap: var(--gap);
  padding: 14px var(--gap);
  transition: border-color 0.15s;
}
.industry-card.selected { border-color: var(--color-primary); background: var(--color-primary-light); }
.ind-icon { font-size: 32px; }
.ind-name { flex: 1; font-size: var(--font-md); font-weight: 600; }
.ind-check { color: var(--color-primary); font-size: 20px; font-weight: bold; }
.nav-btns { display: flex; gap: var(--gap); }
.nav-btns > * { flex: 1; }
.summary-card p { margin: 8px 0; font-size: var(--font-md); }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; text-align: center; }
</style>
