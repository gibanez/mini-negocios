import { api } from './client.js'

export function getCatalog() {
  return api.get('/catalog')
}

// Categories
export function getCategories() {
  return api.get('/catalog/categories')
}

export function createCategory(data) {
  return api.post('/catalog/categories', data)
}

export function updateCategory(id, data) {
  return api.put(`/catalog/categories/${id}`, data)
}

export function deactivateCategory(id) {
  return api.delete(`/catalog/categories/${id}`)
}

// Products
export function getProducts(params = {}) {
  const q = new URLSearchParams()
  if (params.search) q.set('search', params.search)
  if (params.status) q.set('status', params.status)
  if (params.category_id) q.set('category_id', params.category_id)
  const qs = q.toString()
  return api.get('/catalog/products' + (qs ? '?' + qs : ''))
}

export function createProduct(data) {
  return api.post('/catalog/products', data)
}

export function updateProduct(id, data) {
  return api.put(`/catalog/products/${id}`, data)
}

export function deactivateProduct(id) {
  return api.delete(`/catalog/products/${id}`)
}

// Stock
export function adjustStock(productId, data) {
  return api.post(`/catalog/products/${productId}/stock`, data)
}

export function getStockHistory(productId) {
  return api.get(`/catalog/products/${productId}/stock/history`)
}
