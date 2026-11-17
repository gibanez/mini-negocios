import { api } from './client.js'

export function getClients(search) {
  const qs = search ? '?search=' + encodeURIComponent(search) : ''
  return api.get('/clients' + qs)
}

export function getClient(id) {
  return api.get(`/clients/${id}`)
}

export function createClient(data) {
  return api.post('/clients', data)
}

export function updateClient(id, data) {
  return api.put(`/clients/${id}`, data)
}

export function deactivateClient(id) {
  return api.delete(`/clients/${id}`)
}
