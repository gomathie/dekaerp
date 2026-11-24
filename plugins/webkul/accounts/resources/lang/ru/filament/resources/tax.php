<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name'                         => 'Название',
                'tax-type'                     => 'Тип налога',
                'tax-computation'              => 'Расчёт налога',
                'tax-scope'                    => 'Область налога',
                'status'                       => 'Статус',
                'amount'                       => 'Сумма',
                'formula'                      => 'Формула',
                'formula-helper-text'          => 'Только арифметика: числа, + - * / ( ), функции :functions и переменные: :variables',
                'children-taxes'               => 'Дочерние налоги',
                'children-taxes-helper-text'   => 'Сумма этого налога равна сумме его дочерних налогов.',
                'children-taxes-type-mismatch' => 'Дочерние налоги должны иметь тип :type или не иметь типа. Эти не подходят: :taxes',
            ],

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Строки распределения для счетов',
                ],

                'refund-repartition-lines' => [
                    'label' => 'Строки распределения для возвратов',
                ],

                'fields' => [
                    'type'           => 'Тип',
                    'factor-percent' => 'Коэффициент, %',
                    'account'        => 'Счёт',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Дополнительные параметры',

                    'fields' => [
                        'invoice-label'       => 'Название в счёте',
                        'tax-group'           => 'Группа налогов',
                        'country'             => 'Страна',
                        'include-in-price'    => 'Включён в цену',
                        'include-base-amount' => 'Влияет на базу следующих налогов',
                        'is-base-affected'    => 'База зависит от предыдущих налогов',
                    ],
                ],

                'fields' => [
                    'description' => 'Описание',
                    'legal-notes' => 'Юридические примечания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Название',
            'amount-type'            => 'Тип суммы',
            'company'                => 'Компания',
            'tax-group'              => 'Группа налогов',
            'country'                => 'Страна',
            'tax-type'               => 'Тип налога',
            'tax-scope'              => 'Область налога',
            'amount-type'            => 'Тип суммы',
            'invoice-label'          => 'Название в счёте',
            'tax-exigibility'        => 'Момент признания налога',
            'price-include-override' => 'Переопределение включения в цену',
            'amount'                 => 'Сумма',
            'status'                 => 'Статус',
            'include-base-amount'    => 'Включать в базу',
            'is-base-affected'       => 'База зависит',
        ],

        'groups' => [
            'name'         => 'Название',
            'company'      => 'Компания',
            'tax-group'    => 'Группа налогов',
            'country'      => 'Страна',
            'created-by'   => 'Создал',
            'type-tax-use' => 'Применение налога',
            'tax-scope'    => 'Область налога',
            'amount-type'  => 'Тип суммы',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Налог удалён',
                        'body'  => 'Налог успешно удалён.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить налог',
                        'body'  => 'Налог нельзя удалить: он сейчас используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Налоги удалены',
                        'body'  => 'Налоги успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить налоги',
                        'body'  => 'Налоги нельзя удалить: они сейчас используются.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Неверные строки распределения',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Неверные строки распределения',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Название',
                'tax-type'        => 'Тип налога',
                'tax-computation' => 'Расчёт налога',
                'tax-scope'       => 'Область налога',
                'status'          => 'Статус',
                'amount'          => 'Сумма',
                'formula'         => 'Формула',
                'children-taxes'  => 'Дочерние налоги',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Дополнительные параметры',

                    'entries' => [
                        'invoice-label'       => 'Название в счёте',
                        'tax-group'           => 'Группа налогов',
                        'country'             => 'Страна',
                        'include-in-price'    => 'Включать в цену',
                        'include-base-amount' => 'Включать в базу',
                        'is-base-affected'    => 'База зависит',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Описание и юридические примечания в счёте',
                    'entries' => [
                        'description' => 'Описание',
                        'legal-notes' => 'Юридические примечания',
                    ],
                ],
            ],
        ],
    ],

];
