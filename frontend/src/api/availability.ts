import { api, unwrap } from './client'
import type { Availability } from './types'

export function getAvailability(vehicleId: number, date?: string) {
  return unwrap<Availability>(
    api.get(`/vehicles/${vehicleId}/availability`, { params: date ? { date } : undefined }),
  )
}
