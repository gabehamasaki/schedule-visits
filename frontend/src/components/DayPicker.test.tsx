import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { describe, expect, it, vi } from 'vitest'
import type { DayAvailability } from '../api/types'
import DayPicker from './DayPicker'

const days: DayAvailability[] = [
  { date: '2026-09-04', slots: [{ time: '09:00', available: true }] },
  { date: '2026-09-05', slots: [{ time: '09:00', available: false }] },
]

describe('DayPicker', () => {
  it('shows the month of the selected day', () => {
    render(<DayPicker days={days} selectedDate="2026-09-04" onSelect={vi.fn()} />)

    expect(screen.getByRole('heading', { name: 'Setembro 2026' })).toBeInTheDocument()
  })

  it('disables a day whose every hour is taken', async () => {
    const onSelect = vi.fn()

    render(<DayPicker days={days} selectedDate="2026-09-04" onSelect={onSelect} />)

    const soldOut = screen.getByRole('button', { name: /SÁB\s*5/ })

    expect(soldOut).toBeDisabled()

    await userEvent.click(soldOut, { pointerEventsCheck: 0 })
    expect(onSelect).not.toHaveBeenCalled()
  })

  it('reports the picked day', async () => {
    const onSelect = vi.fn()

    render(<DayPicker days={days} selectedDate={null} onSelect={onSelect} />)

    await userEvent.click(screen.getByRole('button', { name: /SEX\s*4/ }))

    expect(onSelect).toHaveBeenCalledWith('2026-09-04')
  })

  it('explains an empty horizon', () => {
    render(<DayPicker days={[]} selectedDate={null} onSelect={vi.fn()} />)

    expect(screen.getByText('Nenhuma data disponível para este veículo.')).toBeInTheDocument()
  })
})
