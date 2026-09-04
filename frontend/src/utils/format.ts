const currency = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
  maximumFractionDigits: 0,
})

/** 13700 => "R$ 13.700" */
export function formatPrice(value: number): string {
  return currency.format(value)
}
