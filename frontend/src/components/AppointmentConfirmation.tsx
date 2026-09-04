import CalendarTodayOutlinedIcon from '@mui/icons-material/CalendarTodayOutlined'
import CheckIcon from '@mui/icons-material/Check'
import LocationOnOutlinedIcon from '@mui/icons-material/LocationOnOutlined'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Divider from '@mui/material/Divider'
import Paper from '@mui/material/Paper'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'
import { colors } from '../theme'
import { formatLongDateTime } from '../utils/format'

type AppointmentConfirmationProps = {
  date: string
  time: string
  location: string
  onBrowseVehicles: () => void
}

export default function AppointmentConfirmation({
  date,
  time,
  location,
  onBrowseVehicles,
}: AppointmentConfirmationProps) {
  return (
    <Paper
      variant="outlined"
      sx={{ bgcolor: colors.surfaceMuted, px: { xs: 3, md: 6 }, py: { xs: 5, md: 7 } }}
    >
      <Stack spacing={3} sx={{ alignItems: 'center', textAlign: 'center' }}>
        {/* Halo plus solid circle, the success mark from the prototype */}
        <Box
          sx={{
            width: 96,
            height: 96,
            borderRadius: '50%',
            bgcolor: colors.brandSoft,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          }}
        >
          <Box
            sx={{
              width: 68,
              height: 68,
              borderRadius: '50%',
              bgcolor: 'primary.main',
              color: 'primary.contrastText',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
            }}
          >
            <CheckIcon sx={{ fontSize: 36 }} />
          </Box>
        </Box>

        <Typography variant="h5" component="h1">
          Agendamento concluído!
        </Typography>

        <Stack
          direction={{ xs: 'column', sm: 'row' }}
          spacing={2}
          divider={<Divider orientation="vertical" flexItem />}
          sx={{ alignItems: 'center', color: 'text.secondary' }}
        >
          <Stack direction="row" spacing={1} sx={{ alignItems: 'center' }}>
            <CalendarTodayOutlinedIcon fontSize="small" />
            <Typography variant="body2">{formatLongDateTime(date, time)}</Typography>
          </Stack>

          <Stack direction="row" spacing={1} sx={{ alignItems: 'center' }}>
            <LocationOnOutlinedIcon fontSize="small" />
            <Typography variant="body2">{location}</Typography>
          </Stack>
        </Stack>

        <Button variant="contained" size="large" onClick={onBrowseVehicles}>
          Outros Veículos
        </Button>
      </Stack>
    </Paper>
  )
}
