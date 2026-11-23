<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name'                         => 'Название',
                'tax-type'                     => 'Налог Тип',
                'tax-computation'              => 'Налог Computation',
                'tax-scope'                    => 'Налог Scope',
                'status'                       => 'Статус',
                'amount'                       => 'Сумма',
                'formula'                      => 'Formula',
                'formula-helper-text'          => 'Arithmetic только: numbers, + - * / ( ), functions :functions и эти variables: :variables',
                'children-taxes'               => 'Children Налоги',
                'children-taxes-helper-text'   => 'сумма этот налог sum его children налоги.',
                'children-taxes-type-mismatch' => 'children налоги нужно быть :type налог тип или есть нет налог тип. Эти не: :taxes',
            ],

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Счёт Repartition Строки',
                ],

                'refund-repartition-lines' => [
                    'label' => 'Возврат Repartition Строки',
                ],

                'fields' => [
                    'type'           => 'Тип',
                    'factor-percent' => 'Factor %',
                    'account'        => 'Счёт',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Advanced Options',

                    'fields' => [
                        'invoice-label'       => 'Счёт подпись',
                        'tax-group'           => 'Налог Группа',
                        'country'             => 'Страна',
                        'include-in-price'    => 'Включено в Цена',
                        'include-base-amount' => 'Affect Base Subsequent Налоги',
                        'is-base-affected'    => 'Base Affected от Предыдущий Налоги',
                    ],
                ],

                'fields' => [
                    'description' => 'Описание',
                    'legal-notes' => 'Legal Заметки',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Название',
            'amount-type'            => 'Сумма Тип',
            'company'                => 'Компания',
            'tax-group'              => 'Налог Группа',
            'country'                => 'Страна',
            'tax-type'               => 'Налог Тип',
            'tax-scope'              => 'Налог Scope',
            'amount-type'            => 'Сумма Тип',
            'invoice-label'          => 'Счёт Подпись',
            'tax-exigibility'        => 'Налог Exigibility',
            'price-include-override' => 'Цена Include Override',
            'amount'                 => 'Сумма',
            'status'                 => 'Статус',
            'include-base-amount'    => 'Include Base Сумма',
            'is-base-affected'       => 'Base Affected',
        ],

        'groups' => [
            'name'         => 'Название',
            'company'      => 'Компания',
            'tax-group'    => 'Налог Группа',
            'country'      => 'Страна',
            'created-by'   => 'Создано От',
            'type-tax-use' => 'Тип Налог Использовать',
            'tax-scope'    => 'Налог Scope',
            'amount-type'  => 'Сумма Тип',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Налог удалено',
                        'body'  => 'Налог есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Налог could не быть удалено',
                        'body'  => 'налог нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Налоги удалено',
                        'body'  => 'налоги есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Налоги could не быть удалено',
                        'body'  => 'налоги нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Недействительно Repartition Строки',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Недействительно Repartition Строки',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Название',
                'tax-type'        => 'Налог Тип',
                'tax-computation' => 'Налог Computation',
                'tax-scope'       => 'Налог Scope',
                'status'          => 'Статус',
                'amount'          => 'Сумма',
                'formula'         => 'Formula',
                'children-taxes'  => 'Children Налоги',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Advanced Options',

                    'entries' => [
                        'invoice-label'       => 'Счёт подпись',
                        'tax-group'           => 'Налог Группа',
                        'country'             => 'Страна',
                        'include-in-price'    => 'Include в цена',
                        'include-base-amount' => 'Include base сумма',
                        'is-base-affected'    => 'base affected',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Описание & Счёт Legal Заметки',
                    'entries' => [
                        'description' => 'Описание',
                        'legal-notes' => 'Legal Заметки',
                    ],
                ],
            ],
        ],
    ],

];
