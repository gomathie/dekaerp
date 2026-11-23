<?php

return [
    'empty' => 'Nothing до отчёт для этот период.',
    'range' => [
        'from'  => 'От',
        'until' => 'До',
    ],
    'columns' => [
        'employee'         => 'Сотрудник',
        'date'             => 'Дата',
        'check_in'         => 'Clock в',
        'late_minutes'     => 'Late (минуты)',
        'worked_minutes'   => 'Worked (минуты)',
        'expected_minutes' => 'Scheduled (минуты)',
        'overtime_minutes' => 'Сверхурочные (минуты)',
    ],
    'lateness' => [
        'title'      => 'Опоздание',
        'subheading' => 'Clock-ins после scheduled начать, beyond компания tolerance.',
    ],
    'overtime' => [
        'title'      => 'Сверхурочные',
        'subheading' => 'Time worked прошлые scheduled length, beyond компания tolerance.',
    ],
    'absence' => [
        'title'      => 'Отсутствие',
        'subheading' => 'Scheduled working дни с нет посещаемость. Утверждён отпуск не counted.',
    ],
];
