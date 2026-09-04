import ChevronLeftIcon from '@mui/icons-material/ChevronLeft'
import ChevronRightIcon from '@mui/icons-material/ChevronRight'
import ButtonBase from '@mui/material/ButtonBase'
import IconButton from '@mui/material/IconButton'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'
import { useState } from 'react'
import type { Slot } from '../api/types'
import { colors } from '../theme'

type HourPickerProps = {
  slots: Slot[]
  selectedTime: string | null
  onSelect: (time: string) => void
}

const pageSize = 6

/** Remount this with a key per date: the window resets with the component. */
export default function HourPicker({ slots, selectedTime, onSelect }: HourPickerProps) {
  const [windowStart, setWindowStart] = useState(0)

  if (slots.length === 0) {
    return (
      <Typography align="center" color="text.secondary">
        Nenhum horário nesta data.
      </Typography>
    )
  }

  const maxStart = Math.max(0, slots.length - pageSize)
  const start = Math.min(windowStart, maxStart)
  const visibleSlots = slots.slice(start, start + pageSize)

  return (
    <Stack direction="row" spacing={1} sx={{ alignItems: 'center', justifyContent: 'center' }}>
      <IconButton
        aria-label="Horários anteriores"
        disabled={start === 0}
        onClick={() => setWindowStart(Math.max(0, start - pageSize))}
      >
        <ChevronLeftIcon />
      </IconButton>

      <Stack direction="row" spacing={1.5} sx={{ justifyContent: 'center', flexWrap: 'wrap' }}>
        {visibleSlots.map((slot) => {
          const isSelected = slot.time === selectedTime

          return (
            <ButtonBase
              key={slot.time}
              // A taken slot stays visible so the grid keeps its shape
              disabled={!slot.available}
              onClick={() => onSelect(slot.time)}
              aria-pressed={isSelected}
              title={slot.available ? undefined : 'Horário já reservado'}
              sx={{
                px: 2.5,
                py: 1.25,
                borderRadius: 6,
                bgcolor: isSelected ? 'primary.main' : colors.chip,
                color: isSelected ? 'primary.contrastText' : 'text.primary',
                fontWeight: 500,
                transition: 'background-color 120ms ease',
                '&:hover': { bgcolor: isSelected ? 'primary.dark' : colors.chipHover },
                '&.Mui-disabled': {
                  color: 'text.disabled',
                  textDecoration: 'line-through',
                  opacity: 0.6,
                },
              }}
            >
              {slot.time}
            </ButtonBase>
          )
        })}
      </Stack>

      <IconButton
        aria-label="Próximos horários"
        disabled={start >= maxStart}
        onClick={() => setWindowStart(Math.min(maxStart, start + pageSize))}
      >
        <ChevronRightIcon />
      </IconButton>
    </Stack>
  )
}
