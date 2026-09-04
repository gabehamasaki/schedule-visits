import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { describe, expect, it, vi } from 'vitest'
import type { Slot } from '../api/types'
import HourPicker from './HourPicker'

function slotsFrom(times: [string, boolean][]): Slot[] {
  return times.map(([time, available]) => ({ time, available }))
}

describe('HourPicker', () => {
  it('disables a booked hour but keeps it on screen', async () => {
    const onSelect = vi.fn()

    render(
      <HourPicker
        slots={slotsFrom([
          ['09:00', false],
          ['10:00', true],
        ])}
        selectedTime={null}
        onSelect={onSelect}
      />,
    )

    const booked = screen.getByRole('button', { name: '09:00' })

    expect(booked).toBeInTheDocument()
    expect(booked).toBeDisabled()
    expect(screen.getByRole('button', { name: '10:00' })).toBeEnabled()

    // pointerEventsCheck is off because MUI puts pointer-events: none on a
    // disabled button, and the point here is that the handler stays silent
    await userEvent.click(booked, { pointerEventsCheck: 0 })
    expect(onSelect).not.toHaveBeenCalled()
  })

  it('reports the picked hour', async () => {
    const onSelect = vi.fn()

    render(<HourPicker slots={slotsFrom([['10:00', true]])} selectedTime={null} onSelect={onSelect} />)

    await userEvent.click(screen.getByRole('button', { name: '10:00' }))

    expect(onSelect).toHaveBeenCalledWith('10:00')
  })

  it('marks the selected hour as pressed', () => {
    render(
      <HourPicker
        slots={slotsFrom([
          ['10:00', true],
          ['11:00', true],
        ])}
        selectedTime="11:00"
        onSelect={vi.fn()}
      />,
    )

    expect(screen.getByRole('button', { name: '11:00' })).toHaveAttribute('aria-pressed', 'true')
    expect(screen.getByRole('button', { name: '10:00' })).toHaveAttribute('aria-pressed', 'false')
  })

  it('pages six hours at a time', async () => {
    const times: [string, boolean][] = [
      ['09:00', true],
      ['10:00', true],
      ['11:00', true],
      ['12:00', true],
      ['13:00', true],
      ['14:00', true],
      ['15:00', true],
      ['16:00', true],
    ]

    render(<HourPicker slots={slotsFrom(times)} selectedTime={null} onSelect={vi.fn()} />)

    expect(screen.getByRole('button', { name: '09:00' })).toBeInTheDocument()
    expect(screen.queryByRole('button', { name: '15:00' })).not.toBeInTheDocument()

    // The left arrow is only useful once the window has moved
    expect(screen.getByRole('button', { name: 'Horários anteriores' })).toBeDisabled()

    await userEvent.click(screen.getByRole('button', { name: 'Próximos horários' }))

    expect(screen.getByRole('button', { name: '15:00' })).toBeInTheDocument()
    expect(screen.queryByRole('button', { name: '09:00' })).not.toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Horários anteriores' })).toBeEnabled()
  })

  it('explains an empty day instead of rendering an empty row', () => {
    render(<HourPicker slots={[]} selectedTime={null} onSelect={vi.fn()} />)

    expect(screen.getByText('Nenhum horário nesta data.')).toBeInTheDocument()
  })
})
