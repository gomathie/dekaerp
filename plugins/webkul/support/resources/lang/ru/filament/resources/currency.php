<?php

return [
    'title' => 'Валюты',

    'navigation' => [
        'title' => 'Валюты',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Сведения о валюте',

                'fields' => [
                    'name'         => 'Название валюты',
                    'name-tooltip' => 'Введите официальное название валюты',
                    'symbol'       => 'Символ валюты',
                    'full-name'    => 'Полное название',
                    'iso-numeric'  => 'Цифровой код ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Формат',

                'fields' => [
                    'decimal-places'        => 'Знаков после запятой',
                    'rounding'              => 'Точность округления',
                    'rounding-helper-text'  => 'Задайте точность округления для расчётов в валюте',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус и настройки',

                'fields' => [
                    'status' => 'Статус',
                ],
            ],

            'rates' => [
                'title'       => 'Курсы валюты',
                'description' => 'Управляйте историей курсов этой валюты относительно базовой валюты (USD).',

                'fields' => [
                    'name'              => 'Дата',
                    'unit-per-currency' => 'Единиц за 1 :currency',
                    'currency-per-unit' => ':currency за единицу',
                ],

                'add-rate'   => 'Добавить курс',
                'item-label' => 'Курс',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Название валюты',
            'symbol'         => 'Символ',
            'full-name'      => 'Полное название',
            'iso-numeric'    => 'Код ISO',
            'decimal-places' => 'Знаков после запятой',
            'rounding'       => 'Округление',
            'status'         => 'Статус',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'groups' => [
            'name'           => 'Название',
            'status'         => 'Статус',
            'decimal-places' => 'Знаков после запятой',
            'creation-date'  => 'Дата создания',
            'last-update'    => 'Последнее обновление',
        ],

        'filters' => [
            'status' => 'Статус',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Валюта удалена',
                    'body'    => 'Валюта успешно удалена.',

                    'success' => [
                        'title' => 'Валюта удалена',
                        'body'  => 'Валюта успешно удалена.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить валюту',
                        'body'  => 'Валюту нельзя удалить, так как она сейчас используется.',
                    ],
                ],
            ],

            'deactivate' => [
                'notification' => [
                    'title' => 'Нельзя деактивировать валюту',
                    'body'  => 'Эту валюту используют одна или несколько компаний, поэтому её нельзя деактивировать.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Валюты удалены',
                    'body'  => 'Валюты успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Сведения о валюте',

                'entries' => [
                    'name'         => 'Название валюты',
                    'symbol'       => 'Символ валюты',
                    'full-name'    => 'Полное название',
                    'iso-numeric'  => 'Цифровой код ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Формат',

                'entries' => [
                    'decimal-places' => 'Знаков после запятой',
                    'rounding'       => 'Точность округления',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус и настройки',

                'entries' => [
                    'status' => 'Статус',
                ],
            ],

            'rates' => [
                'title'       => 'Курсы валюты',

                'entries' => [
                    'name'              => 'Дата',
                    'unit-per-currency' => 'Единиц за 1 :currency',
                    'currency-per-unit' => ':currency за единицу',
                ],
            ],
        ],
    ],
];
