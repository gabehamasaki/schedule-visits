import { useQuery } from '@tanstack/react-query'
import { getAvailability } from '../api/availability'

export function useAvailability(vehicleId: number) {
  return useQuery({
    queryKey: ['availability', vehicleId],
    queryFn: () => getAvailability(vehicleId),
    enabled: Number.isInteger(vehicleId) && vehicleId > 0,
    staleTime: 30_000,
  })
}
