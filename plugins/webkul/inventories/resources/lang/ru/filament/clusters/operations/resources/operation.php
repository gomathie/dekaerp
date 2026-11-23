<?php

return [
    'navigation' => [
        'title' => 'Товары',
        'group' => 'Склад',
    ],

    'global-search' => [
        'partner' => 'Контрагент',
        'origin'  => 'Происхождение',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'receive-from'         => 'Receive От',
                    'contact'              => 'Контакт',
                    'delivery-address'     => 'Доставка Адрес',
                    'operation-type'       => 'Operation Тип',
                    'source-location'      => 'Источник Место',
                    'destination-location' => 'Назначение Место',
                ],
            ],

            'additional-fields' => [
                'title' => 'Additional Сведения',
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Операции',

                'columns' => [
                    'product'                    => 'Товар',
                    'final-location'             => 'Final Место',
                    'description'                => 'Описание',
                    'scheduled-at'               => 'Scheduled В',
                    'deadline'                   => 'Deadline',
                    'packaging'                  => 'Упаковка',
                    'demand'                     => 'Потребность',
                    'quantity'                   => 'Количество',
                    'insufficient-stock-tooltip' => 'Insufficient количество доступно',
                    'unit'                       => 'Единица',
                    'picked'                     => 'Picked',
                ],

                'actions' => [
                    'open-product' => [
                        'tooltip' => 'Открыт товар',
                    ],
                ],

                'fields' => [
                    'product'        => 'Товар',
                    'final-location' => 'Final Место',
                    'description'    => 'Описание',
                    'scheduled-at'   => 'Scheduled В',
                    'deadline'       => 'Deadline',
                    'packaging'      => 'Упаковка',
                    'demand'         => 'Потребность',
                    'quantity'       => 'Количество',
                    'unit'           => 'Единица',
                    'picked'         => 'Picked',

                    'lines' => [
                        'modal-heading'             => 'Manage Запас Moves',
                        'modal-submit-action-label' => 'Сохранить',
                        'add-line'                  => 'Добавить Строка',

                        'actions' => [
                            'generate' => 'Generate Serials/Партии',
                            'import'   => 'Импорт Serials/Партии',
                        ],

                        'fields' => [
                            'lot'                => 'Партия/Серийный номер Номер',
                            'pick-from'          => 'Pick От',
                            'location'           => 'Store До',
                            'package'            => 'Назначение Упаковка',
                            'quantity'           => 'Количество',
                            'uom'                => 'Единица измерения',
                            'first-lot'          => 'Первый Партия Номер',
                            'quantity-per-lot'   => 'Количество за Партия',
                            'quantity-received'  => 'Количество Получено',
                            'keep-current-lines' => 'Оставить текущий строки',
                            'serials'            => 'Партия/Серийный номер Numbers',
                            'serials-helper'     => 'One партия/серийный номер номер за строка.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional',

                'fields' => [
                    'responsible'                  => 'Responsible',
                    'shipping-policy'              => 'Shipping Policy',
                    'shipping-policy-hint-tooltip' => 'Это defines whether goods следует быть delivered partially или все в один раз.',
                    'scheduled-at'                 => 'Scheduled В',
                    'scheduled-at-hint-tooltip'    => 'scheduled time для processing первый part отправка. Вручную настройка значение здесь применить это как ожидается дата для все запас moves.',
                    'source-document'              => 'Источник Документ',
                    'source-document-hint-tooltip' => 'Ссылка документ',
                ],
            ],

            'note' => [
                'title' => 'Заметка',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'Favorite',
            'reference'       => 'Ссылка',
            'from'            => 'От',
            'to'              => 'До',
            'contact'         => 'Контакт',
            'responsible'     => 'Responsible',
            'scheduled-at'    => 'Scheduled В',
            'deadline'        => 'Deadline',
            'closed-at'       => 'Закрыт В',
            'source-document' => 'Источник Документ',
            'operation-type'  => 'Operation Тип',
            'company'         => 'Компания',
            'state'           => 'Состояние',
            'deleted-at'      => 'Удалено',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'groups' => [
            'state'           => 'Состояние',
            'source-document' => 'Источник Документ',
            'operation-type'  => 'Operation Тип',
            'scheduled-at'    => 'График В',
            'created-at'      => 'Создано',
        ],

        'filters' => [
            'operation-type'       => 'Operation Тип',
            'name'                 => 'Название',
            'state'                => 'Состояние',
            'partner'              => 'Контрагент',
            'responsible'          => 'Responsible',
            'owner'                => 'Владелец',
            'source-location'      => 'Источник Место',
            'destination-location' => 'Назначение Место',
            'deadline'             => 'Deadline',
            'scheduled-at'         => 'Scheduled В',
            'closed-at'            => 'Закрыт В',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
            'company'              => 'Компания',
            'creator'              => 'Creator',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'contact'              => 'Контакт',
                    'operation-type'       => 'Operation Тип',
                    'source-location'      => 'Источник Место',
                    'destination-location' => 'Назначение Место',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Операции',
                'entries' => [
                    'product'        => 'Товар',
                    'final-location' => 'Final Место',
                    'description'    => 'Описание',
                    'scheduled-at'   => 'Scheduled В',
                    'deadline'       => 'Deadline',
                    'packaging'      => 'Упаковка',
                    'demand'         => 'Потребность',
                    'quantity'       => 'Количество',
                    'unit'           => 'Единица',
                    'picked'         => 'Picked',
                ],
            ],
            'additional' => [
                'title'   => 'Additional Сведения',
                'entries' => [
                    'responsible'     => 'Responsible',
                    'shipping-policy' => 'Shipping Policy',
                    'scheduled-at'    => 'Scheduled В',
                    'source-document' => 'Источник Документ',
                ],
            ],
            'note' => [
                'title' => 'Заметка',
            ],
        ],
    ],

    'tabs' => [
        'todo'        => 'До',
        'my'          => 'Мой Перемещения',
        'starred'     => 'Starred',
        'draft'       => 'Черновик',
        'waiting'     => 'Waiting',
        'ready'       => 'Ready',
        'late'        => 'Late',
        'done'        => 'Завершён',
        'canceled'    => 'Отменён',
        'back-orders' => 'Назад Заказы',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Единица измерения Precision Предупреждение',
            'body'  => 'Вы с помощью единица измерения smaller чем one используется до запас этот товар. Этот можно lead до rounding issues вкл. зарезервировано quantities. Consider с помощью smallest единица measure для запас valuation, или reduce rounding precision ваш base единица.',
        ],
    ],
];
