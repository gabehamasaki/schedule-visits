import AppBar from '@mui/material/AppBar'
import Box from '@mui/material/Box'
import Container from '@mui/material/Container'
import Link from '@mui/material/Link'
import Toolbar from '@mui/material/Toolbar'
import { Link as RouterLink } from 'react-router-dom'
import Logo from './Logo'

const navItems = ['Vender', 'Comprar', 'Lojas']

export default function AppHeader() {
  return (
    <AppBar position="static" color="transparent" elevation={0} sx={{ borderBottom: 1, borderColor: 'divider' }}>
      <Container maxWidth="lg">
        <Toolbar disableGutters sx={{ minHeight: { xs: 68, md: 84 }, gap: 2 }}>
          <Box component={RouterLink} to="/" sx={{ display: 'flex' }} aria-label="Página inicial">
            <Logo height={32} />
          </Box>

          <Box sx={{ flexGrow: 1 }} />

          <Box sx={{ display: 'flex', gap: { xs: 2, md: 5 } }}>
            {navItems.map((item) => (
              <Link
                key={item}
                href="#"
                underline="none"
                color="text.primary"
                sx={{ fontWeight: 500, fontSize: { xs: 14, md: 16 } }}
              >
                {item}
              </Link>
            ))}
          </Box>
        </Toolbar>
      </Container>
    </AppBar>
  )
}
