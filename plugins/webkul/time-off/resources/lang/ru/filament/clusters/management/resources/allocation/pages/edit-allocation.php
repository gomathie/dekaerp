<?php

return [
    'notification' => [
        'title' => 'Allocation обновлено',
        'body'  => 'allocation есть обновлено successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Allocation удалено',
                'body'  => 'allocation есть удалено successfully.',
            ],
        ],
        'approved' => [
            'title' => 'Утверждён',

            'notification' => [
                'title' => 'Allocation утверждён',
                'body'  => 'allocation есть утверждён successfully.',
            ],
        ],
        'refuse' => [
            'title' => 'Отказать',

            'notification' => [
                'title' => 'Allocation отказано',
                'body'  => 'allocation есть отказано successfully.',
            ],
        ],
        'mark-as-ready-to-confirm' => [
            'title' => 'Mark как Ready до Подтвердить',

            'notification' => [
                'title' => 'Marked как ready до подтвердить',
                'body'  => 'allocation есть marked как ready до подтвердить successfully.',
            ],
        ],
    ],
];
