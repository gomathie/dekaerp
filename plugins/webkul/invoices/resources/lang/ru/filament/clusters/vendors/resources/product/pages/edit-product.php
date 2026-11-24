<?php

return [
    'notification' => [
        'title' => 'Товар обновлён',
        'body'  => 'Товар успешно обновлён.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Обновить количество',
            'modal-heading'             => 'Обновить количество товара',
            'modal-submit-action-label' => 'Обновить',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Количество в наличии',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Товар удалён',
                'body'  => 'Товар успешно удалён.',
            ],
        ],
    ],
];
