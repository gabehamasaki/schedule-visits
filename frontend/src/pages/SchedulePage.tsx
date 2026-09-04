import Typography from '@mui/material/Typography'
import { useParams } from 'react-router-dom'

export default function SchedulePage() {
  const { vehicleId } = useParams()

  return (
    <Typography variant="h5" gutterBottom>
      Agendamento do veículo {vehicleId}
    </Typography>
  )
}
