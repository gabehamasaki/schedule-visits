import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Typography from '@mui/material/Typography'
import type { ReactNode } from 'react'
import { colors } from '../theme'

type PanelCardProps = {
  title: string
  children: ReactNode
}

/** Card with the dark title bar used by the scheduling and the form panels. */
export default function PanelCard({ title, children }: PanelCardProps) {
  return (
    <Card variant="outlined" sx={{ overflow: 'hidden' }}>
      <Box sx={{ bgcolor: colors.panel, px: 3, py: 2 }}>
        <Typography variant="h6" component="h2" align="center" sx={{ color: 'common.white' }}>
          {title}
        </Typography>
      </Box>

      <Box sx={{ px: { xs: 2, md: 4 }, py: { xs: 3, md: 4 } }}>{children}</Box>
    </Card>
  )
}
