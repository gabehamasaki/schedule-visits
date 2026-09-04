import { api, unwrap } from './client'
import type { Vehicle } from './types'

export function getVehicles() {
  return unwrap<Vehicle[]>(api.get('/vehicles'))
}

export function getVehicle(vehicleId: number) {
  return unwrap<Vehicle>(api.get(`/vehicles/${vehicleId}`))
}
