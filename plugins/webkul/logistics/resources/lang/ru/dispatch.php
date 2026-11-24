<?php

return [
    'capacity' => [
        // Warnings, not refusals: the dispatch still goes ahead (D5).
        'weight' => 'Этот рейс везёт :load кг — больше грузоподъёмности транспортного средства (:capacity кг).',
        'volume' => 'Этот рейс везёт :load м³ — больше вместимости транспортного средства (:capacity м³).',
    ],
];
