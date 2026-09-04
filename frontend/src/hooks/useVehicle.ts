import { useQuery } from '@tanstack/react-query'
import { getVehicle, getVehicles } from '../api/vehicles'

export function useVehicles() {
  return useQuery({
    queryKey: ['vehicles'],
    queryFn: getVehicles,
  })
}

export function useVehicle(vehicleId: number) {
  return useQuery({
    queryKey: ['vehicles', vehicleId],
    queryFn: () => getVehicle(vehicleId),
    enabled: Number.isInteger(vehicleId) && vehicleId > 0,
  })
}
