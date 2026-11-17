import { api } from './client.js'

export function getFiadoAccounts() {
  return api.get('/fiado')
}

export function getFiadoAccount(clientId) {
  return api.get(`/fiado/${clientId}`)
}

export function addDebt(clientId, data) {
  return api.post(`/fiado/${clientId}/debts`, data)
}

export function addPayment(clientId, data) {
  return api.post(`/fiado/${clientId}/payments`, data)
}

export function cancelPayment(clientId, movementId, reason) {
  return api.delete(`/fiado/${clientId}/payments/${movementId}`, { reason })
}

export function renewToken(clientId) {
  return api.post(`/fiado/${clientId}/renew-link`)
}

export function getPublicSummary(token) {
  return fetch(`/api/fiado/public/${token}`).then(r => r.json())
}
