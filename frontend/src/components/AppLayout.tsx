import Box from '@mui/material/Box'
import Container from '@mui/material/Container'
import { Outlet } from 'react-router-dom'
import AppHeader from './AppHeader'

export default function AppLayout() {
  return (
    <Box sx={{ minHeight: '100%', display: 'flex', flexDirection: 'column' }}>
      <AppHeader />

      <Container component="main" maxWidth="lg" sx={{ py: { xs: 3, md: 5 }, flexGrow: 1 }}>
        <Outlet />
      </Container>
    </Box>
  )
}
