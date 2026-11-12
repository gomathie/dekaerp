<?php

return [
    'empty' => 'Aucun résultat pour cette période.',
    'range' => [
        'from'  => 'Du',
        'until' => 'Au',
    ],
    'columns' => [
        'employee'         => 'Employé',
        'date'             => 'Date',
        'check_in'         => 'Arrivée',
        'late_minutes'     => 'Retard (minutes)',
        'worked_minutes'   => 'Travaillé (minutes)',
        'expected_minutes' => 'Prévu (minutes)',
        'overtime_minutes' => 'Heures supplémentaires (minutes)',
    ],
    'lateness' => [
        'title'      => 'Retards',
        'subheading' => 'Arrivées après l\'heure prévue, au-delà de la tolérance de la société.',
    ],
    'overtime' => [
        'title'      => 'Heures supplémentaires',
        'subheading' => 'Temps travaillé au-delà de la durée prévue, au-delà de la tolérance.',
    ],
    'absence' => [
        'title'      => 'Absences',
        'subheading' => 'Jours travaillés prévus sans pointage. Les congés approuvés ne comptent pas.',
    ],
];
