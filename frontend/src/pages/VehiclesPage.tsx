import Grid from '@mui/material/Grid'
import Skeleton from '@mui/material/Skeleton'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'
import ErrorState from '../components/ErrorState'
import VehicleCard from '../components/VehicleCard'
import { useVehicles } from '../hooks/useVehicle'

const gridItemSize = { xs: 12, sm: 6, md: 4, lg: 3 }

export default function VehiclesPage() {
  const { data: vehicles, isPending, error, refetch } = useVehicles()

  return (
    <Stack spacing={3}>
      <Stack spacing={0.5}>
        <Typography variant="h5" component="h1">
          Veículos disponíveis
        </Typography>
        <Typography variant="body2" color="text.secondary">
          Escolha um veículo para agendar a sua visita.
        </Typography>
      </Stack>

      {error && <ErrorState message={error.message} onRetry={() => void refetch()} />}

      {isPending && (
        <Grid container spacing={3}>
          {Array.from({ length: 8 }, (_, index) => (
            <Grid key={index} size={gridItemSize}>
              <Skeleton variant="rounded" height={200} />
              <Skeleton sx={{ mt: 1.5 }} width="70%" />
              <Skeleton width="90%" />
              <Skeleton width="40%" />
            </Grid>
          ))}
        </Grid>
      )}

      {vehicles && vehicles.length === 0 && (
        <Typography color="text.secondary">Nenhum veículo disponível no momento.</Typography>
      )}

      {vehicles && vehicles.length > 0 && (
        <Grid container spacing={3}>
          {vehicles.map((vehicle) => (
            <Grid key={vehicle.id} size={gridItemSize}>
              <VehicleCard vehicle={vehicle} to={`/veiculos/${vehicle.id}`} />
            </Grid>
          ))}
        </Grid>
      )}
    </Stack>
  )
}
