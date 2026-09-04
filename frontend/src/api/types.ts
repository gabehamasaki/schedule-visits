export type ApiEnvelope<T> = {
  status: 'success' | 'error'
  message?: string
  data?: T
  errors?: Record<string, string>
}

export type Vehicle = {
  id: number
  brand: string
  model: string
  version: string
  price: number
  location: string
  imageUrl: string
}

export type DayAvailability = {
  date: string
  availableHours: string[]
}

export type Availability = {
  vehicleId: number
  days: DayAvailability[]
}

export type ScheduleVisitPayload = {
  name: string
  email: string
  phone: string
  date: string
  time: string
}

export type Appointment = {
  id: number
  vehicleId: number
  customerName: string
  customerEmail: string
  customerPhone: string
  appointmentDate: string
  appointmentTime: string
}
