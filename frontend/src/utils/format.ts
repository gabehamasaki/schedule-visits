const currency = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
  maximumFractionDigits: 0,
})

/** 13700 => "R$ 13.700" */
export function formatPrice(value: number): string {
  return currency.format(value)
}

/** Parses YYYY-MM-DD as a local date, avoiding the UTC shift of new Date(iso). */
export function parseDate(date: string): Date {
  return new Date(`${date}T00:00:00`)
}

function capitalize(value: string): string {
  return value.charAt(0).toUpperCase() + value.slice(1)
}

/** "2026-09-22" => "Setembro 2026" */
export function formatMonthLabel(date: string): string {
  const parsed = parseDate(date)
  const month = capitalize(parsed.toLocaleDateString('pt-BR', { month: 'long' }))

  return `${month} ${parsed.getFullYear()}`
}

/** "2026-09-22" => "SEG" */
export function formatWeekdayShort(date: string): string {
  return parseDate(date)
    .toLocaleDateString('pt-BR', { weekday: 'short' })
    .replace('.', '')
    .slice(0, 3)
    .toUpperCase()
}

/** "2026-09-22" => "22" */
export function formatDayNumber(date: string): string {
  return String(parseDate(date).getDate())
}

/** ("2026-09-22", "14:00") => "Sábado, 22 de setembro, 14:00 horas" */
export function formatSlotLabel(date: string, time: string): string {
  const weekday = capitalize(parseDate(date).toLocaleDateString('pt-BR', { weekday: 'long' }))
  const dayAndMonth = parseDate(date).toLocaleDateString('pt-BR', { day: 'numeric', month: 'long' })

  return `${weekday}, ${dayAndMonth}, ${time} horas`
}

/** ("2026-09-22", "14:00") => "Segunda-feira, 22 setembro 2026 às 14:00" */
export function formatLongDateTime(date: string, time: string): string {
  const parsed = parseDate(date)
  const weekday = capitalize(parsed.toLocaleDateString('pt-BR', { weekday: 'long' }))
  const month = parsed.toLocaleDateString('pt-BR', { month: 'long' })

  return `${weekday}, ${parsed.getDate()} ${month} ${parsed.getFullYear()} às ${time}`
}

/** Keeps only digits, which is what the API accepts for the phone. */
export function digitsOnly(value: string): string {
  return value.replace(/\D/g, '')
}

/**
 * Progressive BR phone mask: (11) 3456-7890 with ten digits,
 * (11) 98878-8756 with eleven. Anything else is discarded.
 */
export function formatPhone(value: string): string {
  const digits = digitsOnly(value).slice(0, 11)

  if (digits.length === 0) {
    return ''
  }

  if (digits.length <= 2) {
    return `(${digits}`
  }

  const areaCode = digits.slice(0, 2)
  const number = digits.slice(2)

  // Mobile numbers start with 9 and carry an extra digit before the dash.
  // Deciding by the leading digit keeps the dash still while typing.
  const splitAt = number.startsWith('9') || number.length > 8 ? 5 : 4

  if (number.length <= splitAt) {
    return `(${areaCode}) ${number}`
  }

  return `(${areaCode}) ${number.slice(0, splitAt)}-${number.slice(splitAt)}`
}
