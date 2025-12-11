export interface Product {
  id: string
  name: string
  description: string | null
  supplier_price: number
  selling_price: number
  margin: number
  images: string[]
  supplier_id: string | null
  supplier_product_id: string | null
  status: 'active' | 'inactive' | 'out_of_stock'
  created_at: string
  updated_at: string
}

export interface Order {
  id: string
  customer_id: string | null
  total_amount: number
  supplier_cost: number
  margin: number
  status: 'pending' | 'paid' | 'processing' | 'shipped' | 'delivered' | 'cancelled'
  payment_status: 'pending' | 'paid' | 'failed' | 'refunded'
  payment_id: string | null
  payment_gateway: string | null
  shipping_address: ShippingAddress
  tracking_number: string | null
  created_at: string
  updated_at: string
}

export interface OrderItem {
  id: string
  order_id: string
  product_id: string
  quantity: number
  unit_price: number
  supplier_price: number
  margin: number
  products?: Product
}

export interface Supplier {
  id: string
  name: string
  api_type: 'aliexpress' | 'cj_dropshipping' | 'other'
  wallet_balance: number
  status: 'active' | 'inactive'
  created_at: string
}

export interface ShippingAddress {
  name: string
  street: string
  city: string
  country: string
  postalCode: string
  phone: string
}

export interface CartItem {
  product: Product
  quantity: number
}

