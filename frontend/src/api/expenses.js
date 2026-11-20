import { api } from './client.js'

export function getExpenses(params = {}) {
  const q = new URLSearchParams()
  if (params.dateFrom) q.set('dateFrom', params.dateFrom)
  if (params.dateTo)   q.set('dateTo',   params.dateTo)
  if (params.category) q.set('category', params.category)
  const qs = q.toString()
  return api.get('/expenses' + (qs ? '?' + qs : ''))
}

export function createExpense(data) {
  return api.post('/expenses', data)
}

export function updateExpense(id, data) {
  return api.put(`/expenses/${id}`, data)
}

export function deleteExpense(id) {
  return api.delete(`/expenses/${id}`)
}
