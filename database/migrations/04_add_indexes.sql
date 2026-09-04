CREATE INDEX IF NOT EXISTS idx_vehicles_brand_model ON vehicles (brand, model);
CREATE INDEX IF NOT EXISTS idx_appointments_customer_email ON appointments (customer_email);
CREATE INDEX IF NOT EXISTS idx_appointments_appointment_date ON appointments (appointment_date);
