import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { describe, expect, it, vi } from 'vitest'
import ScheduleForm from './ScheduleForm'

function renderForm(overrides: Partial<React.ComponentProps<typeof ScheduleForm>> = {}) {
  const onSubmit = vi.fn()

  render(
    <ScheduleForm
      slotLabel="Sábado, 5 de setembro, 14:00 horas"
      isSubmitting={false}
      apiErrors={{}}
      onSubmit={onSubmit}
      {...overrides}
    />,
  )

  return { onSubmit }
}

describe('ScheduleForm', () => {
  it('shows the chosen slot', () => {
    renderForm()

    expect(screen.getByText('Sábado, 5 de setembro, 14:00 horas')).toBeInTheDocument()
  })

  it('refuses an empty submit without calling the API', async () => {
    const { onSubmit } = renderForm()

    await userEvent.click(screen.getByRole('button', { name: 'Concluir' }))

    expect(screen.getByText('Informe o seu nome.')).toBeInTheDocument()
    expect(screen.getByText('Informe um e-mail válido.')).toBeInTheDocument()
    expect(screen.getByText('Informe o telefone com DDD.')).toBeInTheDocument()
    expect(onSubmit).not.toHaveBeenCalled()
  })

  it('rejects a phone with the wrong number of digits', async () => {
    const { onSubmit } = renderForm()

    await userEvent.type(screen.getByLabelText('Nome'), 'Ana Souza')
    await userEvent.type(screen.getByLabelText('E-mail'), 'ana@example.com')
    await userEvent.type(screen.getByLabelText('Telefone'), '1198')
    await userEvent.click(screen.getByRole('button', { name: 'Concluir' }))

    expect(screen.getByText('Informe o telefone com DDD.')).toBeInTheDocument()
    expect(onSubmit).not.toHaveBeenCalled()
  })

  it('sends the phone as digits only, however it was typed', async () => {
    const { onSubmit } = renderForm()

    await userEvent.type(screen.getByLabelText('Nome'), '  Ana Souza  ')
    await userEvent.type(screen.getByLabelText('E-mail'), 'ana@example.com')
    await userEvent.type(screen.getByLabelText('Telefone'), '11 98878 8756')
    await userEvent.click(screen.getByRole('button', { name: 'Concluir' }))

    expect(onSubmit).toHaveBeenCalledWith({
      name: 'Ana Souza',
      email: 'ana@example.com',
      phone: '11988788756',
    })
  })

  it('lets the API message win over the local one for the same field', async () => {
    renderForm({ apiErrors: { email: 'Este e-mail já possui um agendamento.' } })

    await userEvent.click(screen.getByRole('button', { name: 'Concluir' }))

    expect(screen.getByText('Este e-mail já possui um agendamento.')).toBeInTheDocument()
    expect(screen.queryByText('Informe um e-mail válido.')).not.toBeInTheDocument()
  })

  it('shows a banner for an error no field can fix', () => {
    renderForm({ apiErrors: { time: 'The requested time is not offered for this date.' } })

    expect(screen.getByRole('alert')).toHaveTextContent(
      'The requested time is not offered for this date.',
    )
  })
})
