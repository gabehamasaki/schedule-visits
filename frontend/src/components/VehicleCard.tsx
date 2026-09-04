import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardActionArea from '@mui/material/CardActionArea'
import CardMedia from '@mui/material/CardMedia'
import Divider from '@mui/material/Divider'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'
import LocationOnOutlinedIcon from '@mui/icons-material/LocationOnOutlined'
import { Link as RouterLink } from 'react-router-dom'
import type { Vehicle } from '../api/types'
import { colors } from '../theme'
import { formatPrice } from '../utils/format'

type VehicleCardProps = {
  vehicle: Vehicle
  /** When given, the whole card becomes a link to that route. */
  to?: string
}

export default function VehicleCard({ vehicle, to }: VehicleCardProps) {
  const content = (
    <>
      <CardMedia
        component="img"
        image={vehicle.imageUrl}
        alt={`${vehicle.brand} ${vehicle.model}`}
        sx={{ height: 200, objectFit: 'cover', bgcolor: colors.surfaceMuted }}
      />

      <Box sx={{ p: 2.5 }}>
        <Typography variant="h6" component="h3">
          {vehicle.brand} {vehicle.model}
        </Typography>

        <Typography
          variant="body2"
          color="text.secondary"
          sx={{ textTransform: 'uppercase', minHeight: 40, mt: 0.5 }}
        >
          {vehicle.version}
        </Typography>

        <Typography variant="subtitle1" sx={{ fontWeight: 700, mt: 1.5 }}>
          {formatPrice(vehicle.price)}
        </Typography>

        <Divider sx={{ my: 2 }} />

        <Stack direction="row" spacing={1} sx={{ alignItems: 'center', color: 'text.secondary' }}>
          <LocationOnOutlinedIcon fontSize="small" />
          <Typography variant="body2">{vehicle.location}</Typography>
        </Stack>
      </Box>
    </>
  )

  return (
    <Card variant="outlined" sx={{ height: '100%', overflow: 'hidden' }}>
      {to ? (
        <CardActionArea component={RouterLink} to={to} sx={{ height: '100%' }}>
          {content}
        </CardActionArea>
      ) : (
        content
      )}
    </Card>
  )
}
