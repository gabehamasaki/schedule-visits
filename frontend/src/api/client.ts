import axios, { AxiosError } from 'axios'
import type { ApiEnvelope } from './types'

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api/v1',
  headers: { Accept: 'application/json' },
})

export class ApiError extends Error {
  readonly status: number
  readonly errors: Record<string, string>

  constructor(message: string, status: number, errors: Record<string, string> = {}) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = errors
  }
}

api.interceptors.response.use(
  (response) => response,
  (error: AxiosError<ApiEnvelope<never>>) => {
    const { response } = error

    if (!response) {
      return Promise.reject(new ApiError('Não foi possível conectar ao servidor.', 0))
    }

    return Promise.reject(
      new ApiError(
        response.data?.message ?? 'Erro inesperado ao falar com o servidor.',
        response.status,
        response.data?.errors ?? {},
      ),
    )
  },
)

export async function unwrap<T>(request: Promise<{ data: ApiEnvelope<T> }>): Promise<T> {
  const { data } = await request

  if (data.data === undefined) {
    throw new ApiError('Resposta do servidor sem dados.', 200)
  }

  return data.data
}
