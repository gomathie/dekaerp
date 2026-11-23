<?php

return [
    'title' => 'Изменить Валюта',

    'notification' => [
        'title' => 'Валюта обновлено',
        'body'  => 'валюта есть обновлено successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Валюта удалено',
                'body'  => 'валюта есть удалено successfully.',

                'error' => [
                    'title' => 'Валюта could не быть удалено',
                    'body'  => 'валюта нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
