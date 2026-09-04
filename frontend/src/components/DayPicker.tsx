import ChevronLeftIcon from '@mui/icons-material/ChevronLeft'
import ChevronRightIcon from '@mui/icons-material/ChevronRight'
import ButtonBase from '@mui/material/ButtonBase'
import IconButton from '@mui/material/IconButton'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'
import { useState } from 'react'
import type { DayAvailability } from '../api/types'
import { colors } from '../theme'
import { formatDayNumber, formatMonthLabel, formatWeekdayShort } from '../utils/format'

type DayPickerProps = {
  days: DayAvailability[]
  selectedDate: string | null
  onSelect: (date: string) => void
}

const pageSize = 6

export default function DayPicker({ days, selectedDate, onSelect }: DayPickerProps) {
  const [windowStart, setWindowStart] = useState(0)

  if (days.length === 0) {
    return (
      <Typography align="center" color="text.secondary">
        Nenhuma data disponível para este veículo.
      </Typography>
    )
  }

  // The horizon is wider than a row, so the strip pages instead of scrolling
  const maxStart = Math.max(0, days.length - pageSize)
  const start = Math.min(windowStart, maxStart)
  const visibleDays = days.slice(start, start + pageSize)

  return (
    <Stack spacing={2.5}>
      <Typography variant="h6" component="h3" align="center">
        {formatMonthLabel(selectedDate ?? visibleDays[0].date)}
      </Typography>

      <Stack direction="row" spacing={1} sx={{ alignItems: 'center', justifyContent: 'center' }}>
        <IconButton
          aria-label="Dias anteriores"
          disabled={start === 0}
          onClick={() => setWindowStart(Math.max(0, start - pageSize))}
        >
          <ChevronLeftIcon />
        </IconButton>

        <Stack direction="row" spacing={1.5} sx={{ justifyContent: 'center', flexWrap: 'wrap' }}>
          {visibleDays.map((day) => {
            const isSelected = day.date === selectedDate
            const isSoldOut = !day.slots.some((slot) => slot.available)

            return (
              <ButtonBase
                key={day.date}
                disabled={isSoldOut}
                onClick={() => onSelect(day.date)}
                aria-pressed={isSelected}
                sx={{
                  width: 60,
                  height: 60,
                  borderRadius: '50%',
                  flexDirection: 'column',
                  bgcolor: isSelected ? 'primary.main' : colors.chip,
                  color: isSelected ? 'primary.contrastText' : 'text.primary',
                  opacity: isSoldOut ? 0.4 : 1,
                  transition: 'background-color 120ms ease',
                  '&:hover': { bgcolor: isSelected ? 'primary.dark' : colors.chipHover },
                }}
              >
                <Typography component="span" sx={{ fontSize: 11, lineHeight: 1.4 }}>
                  {formatWeekdayShort(day.date)}
                </Typography>
                <Typography component="span" sx={{ fontSize: 17, fontWeight: 600, lineHeight: 1.2 }}>
                  {formatDayNumber(day.date)}
                </Typography>
              </ButtonBase>
            )
          })}
        </Stack>

        <IconButton
          aria-label="Próximos dias"
          disabled={start >= maxStart}
          onClick={() => setWindowStart(Math.min(maxStart, start + pageSize))}
        >
          <ChevronRightIcon />
        </IconButton>
      </Stack>
    </Stack>
  )
}
