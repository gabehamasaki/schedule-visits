import { Route, Routes } from 'react-router-dom'
import AppLayout from './components/AppLayout'
import NotFoundPage from './pages/NotFoundPage'
import SchedulePage from './pages/SchedulePage'
import VehiclesPage from './pages/VehiclesPage'

export default function App() {
  return (
    <Routes>
      <Route element={<AppLayout />}>
        <Route path="/" element={<VehiclesPage />} />
        <Route path="/veiculos/:vehicleId" element={<SchedulePage />} />
        <Route path="*" element={<NotFoundPage />} />
      </Route>
    </Routes>
  )
}
