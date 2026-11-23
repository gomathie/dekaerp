<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Внутренний Перевод Удалено',
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
