<?php

return [
    'capacity' => [
        // Warnings, not refusals: the dispatch still goes ahead (D5).
        'weight' => 'This trip carries :load kg, over the vehicle’s :capacity kg capacity.',
        'volume' => 'This trip carries :load m³, over the vehicle’s :capacity m³ capacity.',
    ],
];
