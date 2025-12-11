import axios from 'axios'

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000/api'

export const apiClient = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Intercepteur pour gérer les erreurs
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('API Error:', error.response?.data || error.message)
    return Promise.reject(error)
  }
)

// Fonctions API
export const productsApi = {
  getAll: () => apiClient.get('/products'),
  getById: (id: string) => apiClient.get(`/products/${id}`),
  create: (data: any) => apiClient.post('/products', data),
  update: (id: string, data: any) => apiClient.put(`/products/${id}`, data),
  delete: (id: string) => apiClient.delete(`/products/${id}`),
}

export const ordersApi = {
  getAll: (params?: { customer_id?: string; status?: string }) => 
    apiClient.get('/orders', { params }),
  getById: (id: string) => apiClient.get(`/orders/${id}`),
  create: (data: any) => apiClient.post('/orders', data),
  update: (id: string, data: any) => apiClient.put(`/orders/${id}`, data),
}

export const paymentsApi = {
  create: (data: { orderId: string; customerEmail: string; customerName?: string }) =>
    apiClient.post('/payments/create', data),
}

export const suppliersApi = {
  getAll: () => apiClient.get('/suppliers'),
  getById: (id: string) => apiClient.get(`/suppliers/${id}`),
  create: (data: any) => apiClient.post('/suppliers', data),
  update: (id: string, data: any) => apiClient.put(`/suppliers/${id}`, data),
  getWallet: (supplierId: string) => 
    apiClient.get(`/suppliers/wallet?supplier_id=${supplierId}`),
  depositWallet: (data: { supplier_id: string; amount: number; description?: string }) =>
    apiClient.post('/suppliers/wallet', data),
}

export const statsApi = {
  get: () => apiClient.get('/stats'),
}

