<?php

return [
    'label' => 'Создать Счёт поставщика',

    'action' => [
        'notification' => [
            'warning' => [
                'title' => 'Нет invoiceable строки',
                'body'  => 'нет invoiceable строка, пожалуйста сделать sure тот количество есть получено.',
            ],

            'missing-journal' => [
                'title' => 'Учёт не задать вверх',
            ],

            'success' => [
                'title' => 'Счёт поставщика создано',
                'body'  => 'счёт поставщика есть создано successfully.',
            ],
        ],
    ],
];
