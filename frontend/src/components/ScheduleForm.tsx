import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Grid from '@mui/material/Grid'
import Stack from '@mui/material/Stack'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import { useState } from 'react'
import type { ScheduleVisitPayload } from '../api/types'
import { digitsOnly, formatPhone } from '../utils/format'

type ScheduleFormProps = {
  slotLabel: string
  isSubmitting: boolean
  /** Field errors reported by the API, keyed by field name. */
  apiErrors: Record<string, string>
  apiMessage?: string
  onSubmit: (payload: Omit<ScheduleVisitPayload, 'date' | 'time'>) => void
}

type FieldErrors = {
  name?: string
  email?: string
  phone?: string
}

/** Mirrors ScheduleVisitDTO::validate() so the obvious cases fail without a round trip. */
function validate(name: string, email: string, phone: string): FieldErrors {
  const errors: FieldErrors = {}

  if (name.trim() === '') {
    errors.name = 'Informe o seu nome.'
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    errors.email = 'Informe um e-mail válido.'
  }

  const digits = digitsOnly(phone)
  if (digits.length < 10 || digits.length > 11) {
    errors.phone = 'Informe o telefone com DDD.'
  }

  return errors
}

export default function ScheduleForm({
  slotLabel,
  isSubmitting,
  apiErrors,
  apiMessage,
  onSubmit,
}: ScheduleFormProps) {
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [phone, setPhone] = useState('')
  const [errors, setErrors] = useState<FieldErrors>({})

  // The API is the authority: its message wins over the local one for the same field
  const errorFor = (field: keyof FieldErrors) => apiErrors[field] ?? errors[field]

  // Errors on date or time cannot be fixed in this form, so they show as a banner
  const generalError = apiErrors.date ?? apiErrors.time ?? (Object.keys(apiErrors).length === 0 ? apiMessage : undefined)

  function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault()

    const found = validate(name, email, phone)
    setErrors(found)

    if (Object.keys(found).length > 0) {
      return
    }

    onSubmit({ name: name.trim(), email: email.trim(), phone: digitsOnly(phone) })
  }

  return (
    <Box component="form" onSubmit={handleSubmit} noValidate>
      <Stack spacing={3}>
        <Typography variant="h6" component="p" align="center">
          {slotLabel}
        </Typography>

        {generalError && <Alert severity="error">{generalError}</Alert>}

        <TextField
          label="Nome"
          value={name}
          onChange={(event) => setName(event.target.value)}
          error={Boolean(errorFor('name'))}
          helperText={errorFor('name')}
          autoComplete="name"
          fullWidth
        />

        <Grid container spacing={2}>
          <Grid size={{ xs: 12, sm: 6 }}>
            <TextField
              label="E-mail"
              type="email"
              value={email}
              onChange={(event) => setEmail(event.target.value)}
              error={Boolean(errorFor('email'))}
              helperText={errorFor('email')}
              autoComplete="email"
              fullWidth
            />
          </Grid>

          <Grid size={{ xs: 12, sm: 6 }}>
            <TextField
              label="Telefone"
              value={phone}
              onChange={(event) => setPhone(formatPhone(event.target.value))}
              error={Boolean(errorFor('phone'))}
              helperText={errorFor('phone')}
              autoComplete="tel"
              inputMode="tel"
              placeholder="(11) 98878-8756"
              slotProps={{ htmlInput: { maxLength: 15 } }}
              fullWidth
            />
          </Grid>
        </Grid>

        <Box sx={{ display: 'flex', justifyContent: 'center' }}>
          <Button type="submit" variant="contained" size="large" loading={isSubmitting}>
            Concluir
          </Button>
        </Box>
      </Stack>
    </Box>
  )
}
