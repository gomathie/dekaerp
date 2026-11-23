<?php

return [
    'notification' => [
        'title' => 'Товар обновлено',
        'body'  => 'товар есть обновлено successfully.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Обновить Количество',
            'modal-heading'             => 'Обновить Товар Количество',
            'modal-submit-action-label' => 'Обновить',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Вкл. Вручную Количество',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Товар удалено',
                'body'  => 'товар есть удалено successfully.',
            ],
        ],
    ],
];
