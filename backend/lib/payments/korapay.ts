import axios from 'axios'

const KORAPAY_API_URL = 'https://api.korapay.com/v1'

export interface KorapayPaymentRequest {
  amount: number
  currency?: string
  reference: string
  customer: {
    email: string
    name?: string
  }
  redirect_url: string
  metadata?: Record<string, any>
}

export interface KorapayPaymentResponse {
  status: boolean
  message: string
  data: {
    authorization_url: string
    access_code: string
    reference: string
  }
}

/**
 * Créer un paiement Korapay
 */
export async function createKorapayPayment(
  request: KorapayPaymentRequest
): Promise<KorapayPaymentResponse> {
  try {
    const secretKey = process.env.KORAPAY_SECRET_KEY
    if (!secretKey) {
      throw new Error('KORAPAY_SECRET_KEY n\'est pas configuré')
    }

    const response = await axios.post(
      `${KORAPAY_API_URL}/charges`,
      {
        amount: request.amount,
        currency: request.currency || 'USD',
        reference: request.reference,
        customer: request.customer,
        redirect_url: request.redirect_url,
        metadata: request.metadata
      },
      {
        headers: {
          'Authorization': `Bearer ${secretKey}`,
          'Content-Type': 'application/json'
        }
      }
    )

    return response.data
  } catch (error: any) {
    throw new Error(
      `Erreur Korapay: ${error.response?.data?.message || error.message}`
    )
  }
}

/**
 * Vérifier le statut d'un paiement Korapay
 */
export async function verifyKorapayPayment(
  reference: string
): Promise<any> {
  try {
    const secretKey = process.env.KORAPAY_SECRET_KEY
    if (!secretKey) {
      throw new Error('KORAPAY_SECRET_KEY n\'est pas configuré')
    }

    const response = await axios.get(
      `${KORAPAY_API_URL}/charges/${reference}`,
      {
        headers: {
          'Authorization': `Bearer ${secretKey}`
        }
      }
    )

    return response.data
  } catch (error: any) {
    throw new Error(
      `Erreur vérification Korapay: ${error.response?.data?.message || error.message}`
    )
  }
}

