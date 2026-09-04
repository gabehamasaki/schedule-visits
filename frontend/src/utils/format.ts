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
