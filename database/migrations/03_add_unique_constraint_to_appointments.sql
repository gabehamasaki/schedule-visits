CREATE UNIQUE INDEX IF NOT EXISTS ux_appointments_vehicle_date_time
    ON appointments (vehicle_id, appointment_date, appointment_time);
