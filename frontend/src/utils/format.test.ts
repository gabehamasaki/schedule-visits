import { describe, expect, it } from 'vitest'
import {
  digitsOnly,
  formatPhone,
  formatDayNumber,
  formatLongDateTime,
  formatMonthLabel,
  formatPrice,
  formatSlotLabel,
  formatWeekdayShort,
  parseDate,
} from './format'

describe('formatPrice', () => {
  it('formats without cents, as the design shows', () => {
    // Intl separates the symbol with a non-breaking space, not a regular one
    expect(formatPrice(13700)).toBe('R$\u00a013.700')
    expect(formatPrice(850000)).toBe('R$\u00a0850.000')
  })
})

describe('parseDate', () => {
  it('keeps the calendar day, instead of shifting it by the timezone', () => {
    // new Date('2026-09-04') is parsed as UTC and lands on the 3rd in America/Sao_Paulo
    expect(parseDate('2026-09-04').getDate()).toBe(4)
    expect(parseDate('2026-01-01').getMonth()).toBe(0)
  })
})

describe('formatMonthLabel', () => {
  it('capitalizes the month and drops the pt-BR "de"', () => {
    expect(formatMonthLabel('2026-09-04')).toBe('Setembro 2026')
    expect(formatMonthLabel('2026-03-22')).toBe('Março 2026')
  })
})

describe('formatWeekdayShort and formatDayNumber', () => {
  it('gives the three uppercase letters used on the day chips', () => {
    expect(formatWeekdayShort('2026-09-04')).toBe('SEX')
    expect(formatWeekdayShort('2026-09-05')).toBe('SÁB')
    expect(formatDayNumber('2026-09-04')).toBe('4')
  })
})

describe('formatSlotLabel', () => {
  it('reads as the confirmation sentence of the form', () => {
    expect(formatSlotLabel('2026-09-05', '14:00')).toBe('Sábado, 5 de setembro, 14:00 horas')
  })
})

describe('formatLongDateTime', () => {
  it('spells out the appointment on the success screen', () => {
    expect(formatLongDateTime('2026-09-04', '13:00')).toBe('Sexta-feira, 4 setembro 2026 às 13:00')
  })
})

describe('digitsOnly', () => {
  it('strips everything the API does not accept in a phone', () => {
    expect(digitsOnly('11 98878 8756')).toBe('11988788756')
    expect(digitsOnly('(11) 3456-7890')).toBe('1134567890')
  })
})

describe('formatPhone', () => {
  it('masks a mobile number with nine digits', () => {
    expect(formatPhone('11988788756')).toBe('(11) 98878-8756')
  })

  it('masks a landline with eight digits', () => {
    expect(formatPhone('1134567890')).toBe('(11) 3456-7890')
  })

  it('masks while the number is still being typed', () => {
    expect(formatPhone('')).toBe('')
    expect(formatPhone('1')).toBe('(1')
    expect(formatPhone('11')).toBe('(11')
    expect(formatPhone('119')).toBe('(11) 9')
    expect(formatPhone('1198878')).toBe('(11) 98878')
    expect(formatPhone('11988788')).toBe('(11) 98878-8')
  })

  it('keeps the dash in place while a mobile number is typed', () => {
    // Deciding the split by length alone would move the dash on the ninth digit
    expect(formatPhone('11345')).toBe('(11) 345')
    expect(formatPhone('113456')).toBe('(11) 3456')
    expect(formatPhone('1134567')).toBe('(11) 3456-7')
    expect(formatPhone('119887')).toBe('(11) 9887')
    expect(formatPhone('1198878')).toBe('(11) 98878')
  })

  it('ignores anything that is not a digit and stops at eleven', () => {
    expect(formatPhone('(11) 98878-8756')).toBe('(11) 98878-8756')
    expect(formatPhone('11a98878b8756')).toBe('(11) 98878-8756')
    expect(formatPhone('119887887561234')).toBe('(11) 98878-8756')
  })
})
