<?php

return [
    'first_slot' => $_ENV['SCHEDULE_FIRST_SLOT'] ?? '09:00',
    'last_slot' => $_ENV['SCHEDULE_LAST_SLOT'] ?? '18:00',
    'slot_minutes' => (int) ($_ENV['SCHEDULE_SLOT_MINUTES'] ?? 60),
];
