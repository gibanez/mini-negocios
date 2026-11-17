import { api } from './client.js'

export function getOnboardingStatus() {
  return api.get('/onboarding')
}

export function completeOnboarding(data) {
  return api.post('/onboarding/complete', data)
}

export function getIndustries() {
  return api.get('/industries')
}

export function chooseIndustry(industryId) {
  return api.post('/onboarding/industry', { industry_id: industryId })
}
