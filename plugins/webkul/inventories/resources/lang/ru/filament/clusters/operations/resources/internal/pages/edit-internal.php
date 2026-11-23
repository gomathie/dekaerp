<?php

return [
    'notification' => [
        'title' => 'Внутренний Перевод обновлено',
        'body'  => 'внутренний перевод есть обновлено successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Внутренний Перевод удалено',
                    'body'  => 'внутренний перевод есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Внутренний Перевод could не быть удалено',
                    'body'  => 'внутренний перевод нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
