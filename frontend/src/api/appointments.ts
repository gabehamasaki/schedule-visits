import { api, unwrap } from './client'
import type { Appointment, ScheduleVisitPayload } from './types'

export function scheduleVisit(vehicleId: number, payload: ScheduleVisitPayload) {
  return unwrap<Appointment>(api.post(`/vehicles/${vehicleId}/appointments`, payload))
}
