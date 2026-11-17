import { api } from './client.js'

export function login(email, password) {
  return api.post('/auth/login', { email, password })
}

export function register(storeName, ownerName, email, password) {
  return api.post('/auth/register', { storeName, ownerName, email, password })
}
