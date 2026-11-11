<?php

return [
    'empty' => 'No hay resultados para este periodo.',
    'range' => [
        'from'  => 'Desde',
        'until' => 'Hasta',
    ],
    'columns' => [
        'employee'         => 'Empleado',
        'date'             => 'Fecha',
        'check_in'         => 'Entrada',
        'late_minutes'     => 'Retraso (minutos)',
        'worked_minutes'   => 'Trabajado (minutos)',
        'expected_minutes' => 'Previsto (minutos)',
        'overtime_minutes' => 'Horas extra (minutos)',
    ],
    'lateness' => [
        'title'      => 'Retrasos',
        'subheading' => 'Entradas después de la hora prevista, por encima de la tolerancia de la empresa.',
    ],
    'overtime' => [
        'title'      => 'Horas extra',
        'subheading' => 'Tiempo trabajado por encima de la duración prevista y de la tolerancia.',
    ],
    'absence' => [
        'title'      => 'Ausencias',
        'subheading' => 'Días laborables previstos sin asistencia. Los permisos aprobados no cuentan.',
    ],
];
