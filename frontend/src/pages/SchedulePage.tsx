import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Grid from '@mui/material/Grid'
import Skeleton from '@mui/material/Skeleton'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'
import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import BackLink from '../components/BackLink'
import DayPicker from '../components/DayPicker'
import ErrorState from '../components/ErrorState'
import HourPicker from '../components/HourPicker'
import PanelCard from '../components/PanelCard'
import VehicleCard from '../components/VehicleCard'
import { useAvailability } from '../hooks/useAvailability'
import { useVehicle } from '../hooks/useVehicle'
import { formatSlotLabel } from '../utils/format'

type Step = 'slot' | 'form'

export default function SchedulePage() {
  const { vehicleId: vehicleIdParam } = useParams()
  const vehicleId = Number(vehicleIdParam)
  const navigate = useNavigate()

  const vehicleQuery = useVehicle(vehicleId)
  const availabilityQuery = useAvailability(vehicleId)

  const [step, setStep] = useState<Step>('slot')
  const [selectedDate, setSelectedDate] = useState<string | null>(null)
  const [selectedTime, setSelectedTime] = useState<string | null>(null)

  const days = availabilityQuery.data?.days ?? []

  // The first day with a free hour opens selected, so the hours show up right away
  const activeDate =
    selectedDate ?? days.find((day) => day.slots.some((slot) => slot.available))?.date ?? null
  const slots = days.find((day) => day.date === activeDate)?.slots ?? []
  const hasSlot = activeDate !== null && selectedTime !== null

  function handleSelectDate(date: string) {
    setSelectedDate(date)
    // The new day has its own hours, so the previous pick no longer applies
    setSelectedTime(null)
  }

  if (!Number.isInteger(vehicleId) || vehicleId <= 0) {
    return <ErrorState title="Veículo inválido" message="O endereço não aponta para um veículo." />
  }

  return (
    <Stack spacing={3}>
      <BackLink onClick={() => (step === 'form' ? setStep('slot') : navigate('/'))} />

      {vehicleQuery.error && (
        <ErrorState
          title="Veículo não encontrado"
          message={vehicleQuery.error.message}
          onRetry={() => void vehicleQuery.refetch()}
        />
      )}

      <Grid container spacing={3} sx={{ alignItems: 'flex-start' }}>
        <Grid size={{ xs: 12, md: 4 }}>
          {vehicleQuery.isPending && <Skeleton variant="rounded" height={420} />}
          {vehicleQuery.data && <VehicleCard vehicle={vehicleQuery.data} />}
        </Grid>

        <Grid size={{ xs: 12, md: 8 }}>
          {step === 'slot' ? (
            <PanelCard title="Agende o dia e horário da sua visita">
              {availabilityQuery.isPending && (
                <Stack spacing={2}>
                  <Skeleton width={180} sx={{ mx: 'auto' }} />
                  <Skeleton variant="rounded" height={60} />
                  <Skeleton variant="rounded" height={44} />
                </Stack>
              )}

              {availabilityQuery.error && (
                <ErrorState
                  title="Não foi possível carregar as datas"
                  message={availabilityQuery.error.message}
                  onRetry={() => void availabilityQuery.refetch()}
                />
              )}

              {availabilityQuery.data && (
                <Stack spacing={3}>
                  <DayPicker days={days} selectedDate={activeDate} onSelect={handleSelectDate} />

                  <HourPicker
                    key={activeDate}
                    slots={slots}
                    selectedTime={selectedTime}
                    onSelect={setSelectedTime}
                  />

                  <Box sx={{ display: 'flex', justifyContent: 'center' }}>
                    <Button
                      variant="contained"
                      size="large"
                      disabled={!hasSlot}
                      onClick={() => setStep('form')}
                    >
                      Agendar Visita
                    </Button>
                  </Box>
                </Stack>
              )}
            </PanelCard>
          ) : (
            <PanelCard title="Concluir Agendamento">
              <Typography variant="h6" component="p" align="center">
                {formatSlotLabel(activeDate!, selectedTime!)}
              </Typography>
            </PanelCard>
          )}
        </Grid>
      </Grid>
    </Stack>
  )
}
