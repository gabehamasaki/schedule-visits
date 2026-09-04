import { screen, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { Route, Routes } from 'react-router-dom'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { ApiError } from '../api/client'
import { renderWithProviders } from '../test/renderWithProviders'
import SchedulePage from './SchedulePage'

vi.mock('../api/vehicles', () => ({
  getVehicle: vi.fn(),
  getVehicles: vi.fn(),
}))

vi.mock('../api/availability', () => ({
  getAvailability: vi.fn(),
}))

vi.mock('../api/appointments', () => ({
  scheduleVisit: vi.fn(),
}))

const { getVehicle } = await import('../api/vehicles')
const { getAvailability } = await import('../api/availability')
const { scheduleVisit } = await import('../api/appointments')

const vehicle = {
  id: 1,
  brand: 'BMW',
  model: 'X5',
  version: '3.0 4X4 XDRIVE M SPORT',
  price: 520000,
  location: 'Barueri - SP',
  imageUrl: 'https://example.com/x5.jpg',
}

const availability = {
  vehicleId: 1,
  days: [
    {
      date: '2026-09-05',
      slots: [
        { time: '09:00', available: false },
        { time: '10:00', available: true },
      ],
    },
  ],
}

function renderPage() {
  return renderWithProviders(
    <Routes>
      <Route path="/veiculos/:vehicleId" element={<SchedulePage />} />
      <Route path="/" element={<h1>Veículos disponíveis</h1>} />
    </Routes>,
    { route: '/veiculos/1' },
  )
}

describe('SchedulePage', () => {
  beforeEach(() => {
    vi.mocked(getVehicle).mockResolvedValue(vehicle)
    vi.mocked(getAvailability).mockResolvedValue(availability)
    vi.mocked(scheduleVisit).mockReset()
  })

  it('books a visit from the picked slot and confirms it', async () => {
    vi.mocked(scheduleVisit).mockResolvedValue({
      id: 42,
      vehicleId: 1,
      customerName: 'Ana Souza',
      customerEmail: 'ana@example.com',
      customerPhone: '11988788756',
      appointmentDate: '2026-09-05',
      appointmentTime: '10:00',
    })

    renderPage()

    expect(await screen.findByText('BMW X5')).toBeInTheDocument()

    // The only free hour of the day is the one that can be picked
    expect(await screen.findByRole('button', { name: '09:00' })).toBeDisabled()
    await userEvent.click(screen.getByRole('button', { name: '10:00' }))

    await userEvent.click(screen.getByRole('button', { name: 'Agendar Visita' }))

    expect(screen.getByText('Sábado, 5 de setembro, 10:00 horas')).toBeInTheDocument()

    await userEvent.type(screen.getByLabelText('Nome'), 'Ana Souza')
    await userEvent.type(screen.getByLabelText('E-mail'), 'ana@example.com')
    await userEvent.type(screen.getByLabelText('Telefone'), '11 98878 8756')
    await userEvent.click(screen.getByRole('button', { name: 'Concluir' }))

    expect(await screen.findByText('Agendamento concluído!')).toBeInTheDocument()
    expect(screen.getByText(/Sábado, 5 setembro 2026 às 10:00/)).toBeInTheDocument()
    expect(screen.getByText('Barueri - SP')).toBeInTheDocument()

    expect(scheduleVisit).toHaveBeenCalledWith(1, {
      name: 'Ana Souza',
      email: 'ana@example.com',
      phone: '11988788756',
      date: '2026-09-05',
      time: '10:00',
    })
  })

  it('keeps the button disabled until an hour is picked', async () => {
    renderPage()

    expect(await screen.findByRole('button', { name: 'Agendar Visita' })).toBeDisabled()

    await userEvent.click(screen.getByRole('button', { name: '10:00' }))

    expect(screen.getByRole('button', { name: 'Agendar Visita' })).toBeEnabled()
  })

  it('refreshes the schedule when someone else takes the slot first', async () => {
    vi.mocked(scheduleVisit).mockRejectedValue(
      new ApiError('This time slot is already booked.', 409),
    )

    renderPage()

    await userEvent.click(await screen.findByRole('button', { name: '10:00' }))
    await userEvent.click(screen.getByRole('button', { name: 'Agendar Visita' }))

    await userEvent.type(screen.getByLabelText('Nome'), 'Ana Souza')
    await userEvent.type(screen.getByLabelText('E-mail'), 'ana@example.com')
    await userEvent.type(screen.getByLabelText('Telefone'), '11988788756')
    await userEvent.click(screen.getByRole('button', { name: 'Concluir' }))

    expect(await screen.findByRole('alert')).toHaveTextContent('This time slot is already booked.')

    // The grid is refetched, so it reflects whoever got there first
    await waitFor(() => expect(getAvailability).toHaveBeenCalledTimes(2))
  })

  it('reports a vehicle that does not exist', async () => {
    vi.mocked(getVehicle).mockRejectedValue(new ApiError('Vehicle not found.', 404))

    renderPage()

    expect(await screen.findByText('Veículo não encontrado')).toBeInTheDocument()
  })
})
