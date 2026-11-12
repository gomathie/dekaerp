<?php

return [
    'empty' => 'Nada a reportar neste período.',
    'range' => [
        'from'  => 'De',
        'until' => 'Até',
    ],
    'columns' => [
        'employee'         => 'Colaborador',
        'date'             => 'Data',
        'check_in'         => 'Entrada',
        'late_minutes'     => 'Atraso (minutos)',
        'worked_minutes'   => 'Trabalhado (minutos)',
        'expected_minutes' => 'Previsto (minutos)',
        'overtime_minutes' => 'Horas extra (minutos)',
    ],
    'lateness' => [
        'title'      => 'Atrasos',
        'subheading' => 'Entradas depois da hora prevista, acima da tolerância da empresa.',
    ],
    'overtime' => [
        'title'      => 'Horas extra',
        'subheading' => 'Tempo trabalhado além da duração prevista e da tolerância.',
    ],
    'absence' => [
        'title'      => 'Ausências',
        'subheading' => 'Dias úteis previstos sem presença. As licenças aprovadas não contam.',
    ],
];
