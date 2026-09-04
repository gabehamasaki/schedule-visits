import Box from '@mui/material/Box'

type LogoProps = {
  /** `extended` carries the logotype, `mark` is the symbol alone. */
  variant?: 'extended' | 'mark'
  height?: number
}

export default function Logo({ variant = 'extended', height = 34 }: LogoProps) {
  const file = variant === 'extended' ? '/logo_extended.svg' : '/logo.svg'

  return <Box component="img" src={file} alt="logoipsum" sx={{ height, display: 'block' }} />
}
