<?php

return [
    'navigation' => [
        'title' => 'Места',
        'group' => 'Склад Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'location'                     => 'Место',
                    'location-placeholder'         => 'eg. Spare Запас',
                    'parent-location'              => 'Parent Место',
                    'parent-location-hint-tooltip' => 'main место тот encompasses этот место. Для пример, \'Dispatch Zone\' part \'Gate 1\' parent место.',
                    'external-notes'               => 'Внешний Заметки',
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',

                'fields' => [
                    'location-type'                 => 'Место Тип',
                    'company'                       => 'Компания',
                    'storage-category'              => 'Storage Категория',
                    'is-scrap'                      => 'Scrap Место?',
                    'is-scrap-hint-tooltip'         => 'Выбрать этот checkbox до designate этот место для storing scrapped или damaged goods.',
                    'is-dock'                       => 'Dock Место?',
                    'is-dock-hint-tooltip'          => 'Выбрать этот checkbox до designate этот место для storing goods тот ready для отправка.',
                    'is-replenish'                  => 'Replenish Место?',
                    'is-replenish-hint-tooltip'     => 'Включить этот function до retrieve все quantities needed для replenishment в этот место.',
                    'logistics'                     => 'Логистика',
                    'removal-strategy'              => 'Removal Strategy',
                    'removal-strategy-hint-tooltip' => 'Specifies по умолчанию method для determining exact shelf, партия, и место от который до pick товары. Этот method можно быть enforced в товар категория level, с fallback до parent места если не задать здесь.',
                    'cyclic-counting'               => 'Cyclic Counting',
                    'inventory-frequency'           => 'Склад Frequency',
                    'last-inventory'                => 'Последний Склад',
                    'last-inventory-hint-tooltip'   => 'Дата последний склад в этот место.',
                    'next-expected'                 => 'Далее ожидается',
                    'next-expected-hint-tooltip'    => 'Дата для далее запланировано склад based вкл. cyclic график.',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Сведения',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'Место',
            'type'             => 'Тип',
            'storage-category' => 'Storage Категория',
            'company'          => 'Компания',
            'deleted-at'       => 'удалено',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
        ],

        'groups' => [
            'warehouse'       => 'Склад',
            'type'            => 'Тип',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'filters' => [
            'location' => 'Место',
            'type'     => 'Тип',
            'company'  => 'Компания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Место обновлено',
                    'body'  => 'место есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Место restored',
                    'body'  => 'место есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Место удалено',
                    'body'  => 'место есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Место force удалено',
                        'body'  => 'место есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Место could не быть удалено',
                        'body'  => 'место нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Печать Штрихкод',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Места restored',
                    'body'  => 'места есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Места удалено',
                    'body'  => 'места есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Места force удалено',
                        'body'  => 'места есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Места could не быть удалено',
                        'body'  => 'места нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'entries' => [
                    'location'                     => 'Место',
                    'location-placeholder'         => 'eg. Spare Запас',
                    'parent-location'              => 'Parent Место',
                    'parent-location-hint-tooltip' => 'main место тот encompasses этот место. Для пример, \'Dispatch Zone\' part \'Gate 1\' parent место.',
                    'external-notes'               => 'Внешний Заметки',
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',

                'entries' => [
                    'location-type'                 => 'Место Тип',
                    'company'                       => 'Компания',
                    'storage-category'              => 'Storage Категория',
                    'is-scrap'                      => 'Scrap Место?',
                    'is-scrap-hint-tooltip'         => 'Выбрать этот checkbox до designate этот место для storing scrapped или damaged goods.',
                    'is-dock'                       => 'Dock Место?',
                    'is-dock-hint-tooltip'          => 'Выбрать этот checkbox до designate этот место для storing goods тот ready для отправка.',
                    'is-replenish'                  => 'Replenish Место?',
                    'is-replenish-hint-tooltip'     => 'Включить этот function до retrieve все quantities needed для replenishment в этот место.',
                    'logistics'                     => 'Логистика',
                    'removal-strategy'              => 'Removal Strategy',
                    'removal-strategy-hint-tooltip' => 'Specifies по умолчанию method для determining exact shelf, партия, и место от который до pick товары. Этот method можно быть enforced в товар категория level, с fallback до parent места если не задать здесь.',
                    'cyclic-counting'               => 'Cyclic Counting',
                    'inventory-frequency'           => 'Склад Frequency',
                    'last-inventory'                => 'Последний Склад',
                    'last-inventory-hint-tooltip'   => 'Дата последний склад в этот место.',
                    'next-expected'                 => 'Далее ожидается',
                    'next-expected-hint-tooltip'    => 'Дата для далее запланировано склад based вкл. cyclic график.',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Сведения',
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'created-by'   => 'Создано От',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],
        ],
    ],
];
