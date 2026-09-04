import { useMutation, useQueryClient } from '@tanstack/react-query'
import { scheduleVisit } from '../api/appointments'
import type { ScheduleVisitPayload } from '../api/types'

export function useScheduleVisit(vehicleId: number) {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (payload: ScheduleVisitPayload) => scheduleVisit(vehicleId, payload),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['availability', vehicleId] })
    },
  })
}
