CREATE TABLE IF NOT EXISTS vehicle_availability_slots (
    id SERIAL PRIMARY KEY,
    vehicle_id INT NOT NULL REFERENCES vehicles(id) ON DELETE CASCADE,
    slot_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    UNIQUE (vehicle_id, slot_date, slot_time)
);

CREATE INDEX IF NOT EXISTS idx_availability_slots_vehicle_date
    ON vehicle_availability_slots (vehicle_id, slot_date);
