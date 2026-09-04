import ChevronLeftIcon from '@mui/icons-material/ChevronLeft'
import Link from '@mui/material/Link'
import type { ReactNode } from 'react'

type BackLinkProps = {
  onClick: () => void
  children?: ReactNode
}

export default function BackLink({ onClick, children = 'Voltar' }: BackLinkProps) {
  return (
    <Link
      component="button"
      type="button"
      onClick={onClick}
      underline="none"
      color="text.primary"
      sx={{ display: 'inline-flex', alignItems: 'center', gap: 0.5, fontSize: 16 }}
    >
      <ChevronLeftIcon fontSize="small" />
      {children}
    </Link>
  )
}
