import { api } from './client.js'

export const platform = {
  stats:           ()             => api.get('/platform/stats'),
  listStores:      ()             => api.get('/platform/stores'),
  getStore:        (id)           => api.get(`/platform/stores/${id}`),
  updateStore:     (id, data)     => api.put(`/platform/stores/${id}`, data),
  suspendStore:    (id, reason)   => api.post(`/platform/stores/${id}/suspend`, { reason }),
  activateStore:   (id)           => api.post(`/platform/stores/${id}/activate`),
  listAdmins:      (storeId)      => api.get(`/platform/stores/${storeId}/admins`),
  createAdmin:     (storeId, data)=> api.post(`/platform/stores/${storeId}/admins`, data),
  deactivateAdmin: (adminId)      => api.delete(`/platform/admins/${adminId}`),
}
