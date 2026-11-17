import { api } from './client.js'

export function getSales(params = {}) {
  const q = new URLSearchParams()
  if (params.dateFrom) q.set('dateFrom', params.dateFrom)
  if (params.dateTo)   q.set('dateTo',   params.dateTo)
  if (params.clientId) q.set('clientId', params.clientId)
  const qs = q.toString()
  return api.get('/sales' + (qs ? '?' + qs : ''))
}

export function getSale(id) {
  return api.get(`/sales/${id}`)
}

export function createSale(data) {
  return api.post('/sales', data)
}

export function cancelSale(id, reason) {
  return api.post(`/sales/${id}/cancel`, { reason })
}
