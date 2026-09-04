import Button from '@mui/material/Button'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'
import { Link as RouterLink } from 'react-router-dom'

export default function NotFoundPage() {
  return (
    <Stack spacing={2} sx={{ alignItems: 'flex-start' }}>
      <Typography variant="h5">Página não encontrada</Typography>
      <Button component={RouterLink} to="/" variant="contained">
        Ver veículos
      </Button>
    </Stack>
  )
}
