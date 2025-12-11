// Types pour la base de données Supabase
export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[]

export interface Database {
  public: {
    Tables: {
      customers: {
        Row: {
          id: string
          email: string
          name: string | null
          phone: string | null
          addresses: Json | null
          created_at: string
          updated_at: string
        }
        Insert: {
          id?: string
          email: string
          name?: string | null
          phone?: string | null
          addresses?: Json | null
          created_at?: string
          updated_at?: string
        }
        Update: {
          id?: string
          email?: string
          name?: string | null
          phone?: string | null
          addresses?: Json | null
          created_at?: string
          updated_at?: string
        }
      }
      products: {
        Row: {
          id: string
          name: string
          description: string | null
          supplier_price: number
          selling_price: number
          margin: number
          images: string[]
          supplier_id: string | null
          supplier_product_id: string | null
          status: string
          created_at: string
          updated_at: string
        }
        Insert: {
          id?: string
          name: string
          description?: string | null
          supplier_price: number
          selling_price: number
          images?: string[]
          supplier_id?: string | null
          supplier_product_id?: string | null
          status?: string
          created_at?: string
          updated_at?: string
        }
        Update: {
          id?: string
          name?: string
          description?: string | null
          supplier_price?: number
          selling_price?: number
          images?: string[]
          supplier_id?: string | null
          supplier_product_id?: string | null
          status?: string
          created_at?: string
          updated_at?: string
        }
      }
      orders: {
        Row: {
          id: string
          customer_id: string | null
          total_amount: number
          supplier_cost: number
          margin: number
          status: string
          payment_status: string
          payment_id: string | null
          payment_gateway: string | null
          shipping_address: Json
          tracking_number: string | null
          created_at: string
          updated_at: string
        }
        Insert: {
          id?: string
          customer_id?: string | null
          total_amount: number
          supplier_cost?: number
          margin?: number
          status?: string
          payment_status?: string
          payment_id?: string | null
          payment_gateway?: string | null
          shipping_address: Json
          tracking_number?: string | null
          created_at?: string
          updated_at?: string
        }
        Update: {
          id?: string
          customer_id?: string | null
          total_amount?: number
          supplier_cost?: number
          margin?: number
          status?: string
          payment_status?: string
          payment_id?: string | null
          payment_gateway?: string | null
          shipping_address?: Json
          tracking_number?: string | null
          created_at?: string
          updated_at?: string
        }
      }
      suppliers: {
        Row: {
          id: string
          name: string
          api_type: string
          api_key: string | null
          api_secret: string | null
          wallet_balance: number
          status: string
          created_at: string
          updated_at: string
        }
        Insert: {
          id?: string
          name: string
          api_type: string
          api_key?: string | null
          api_secret?: string | null
          wallet_balance?: number
          status?: string
          created_at?: string
          updated_at?: string
        }
        Update: {
          id?: string
          name?: string
          api_type?: string
          api_key?: string | null
          api_secret?: string | null
          wallet_balance?: number
          status?: string
          created_at?: string
          updated_at?: string
        }
      }
    }
  }
}

