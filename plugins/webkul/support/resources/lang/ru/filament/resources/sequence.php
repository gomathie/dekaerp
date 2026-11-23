<?php

return [
    'model-label' => 'Нумерация',

    'plural-model-label' => 'Sequences',

    'navigation' => [
        'title' => 'Sequences',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'Общее',
                'description' => 'Sequences для журналы, склады и operation types создано автоматически когда те записи создано — изменить их здесь. Вручную creation только needed для свой код-based sequences.',

                'fields' => [
                    'name'      => 'Название',
                    'code'      => 'Код',
                    'code-help' => 'Technical identifier используется от документы, e.g. продажи.заказ. Auto-создано sequences уже есть справа код.',
                    'company'   => 'Компания',
                ],
            ],

            'format' => [
                'title' => 'Numbering',

                'fields' => [
                    'prefix'           => 'Префикс',
                    'prefix-help'      => 'Placeholders: %(год), %(y), %(месяц), %(день). Пример: INV/%(год)/',
                    'suffix'           => 'Суффикс',
                    'padding'          => 'Номер Padding',
                    'next-number'      => 'Далее Номер',
                    'next-number-help' => 'Можно только быть increased. До restart numbering safely, удалить нумерация — это быть recreated от highest existing документ номер.',
                    'step'             => 'Step',
                    'reset-frequency'  => 'Сбросить Counter',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Название',
            'code'            => 'Код',
            'applies-to'      => 'Действует До',
            'company'         => 'Компания',
            'next-preview'    => 'Далее Документ Номер',
            'next-number'     => 'Далее Номер',
            'reset-frequency' => 'Сбросить Counter',
        ],

        'variants' => [
            'refund'         => 'Возврат',
            'payment'        => 'Оплата',
            'refund-payment' => 'Возврат + Оплата',
        ],

        'filters' => [
            'company' => 'Компания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Нумерация обновлено',
                    'body'  => 'нумерация есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Нумерация удалено',
                    'body'  => 'нумерация есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sequences удалено',
                    'body'  => 'sequences есть удалено successfully.',
                ],
            ],
        ],
    ],
];
