<?php

return [
    'notification' => [
        'title' => 'Товар обновлён',
        'body'  => 'Товар успешно обновлён.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Изменить количество',
            'modal-heading'             => 'Изменить количество товара',
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
