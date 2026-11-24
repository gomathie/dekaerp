<?php

return [
    'navigation' => [
        'title' => 'Товары',
        'group' => 'Запасы',
    ],

    'global-search' => [
        'partner' => 'Контакт',
        'origin'  => 'Источник',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'receive-from'         => 'Получить от',
                    'contact'              => 'Контакт',
                    'delivery-address'     => 'Адрес доставки',
                    'operation-type'       => 'Тип операции',
                    'source-location'      => 'Исходное местоположение',
                    'destination-location' => 'Местоположение назначения',
                ],
            ],

            'additional-fields' => [
                'title' => 'Дополнительная информация',
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Операции',

                'columns' => [
                    'product'                    => 'Товар',
                    'final-location'             => 'Конечное местоположение',
                    'description'                => 'Описание',
                    'scheduled-at'               => 'Запланировано на',
                    'deadline'                   => 'Крайний срок',
                    'packaging'                  => 'Фасовка',
                    'demand'                     => 'Потребность',
                    'quantity'                   => 'Количество',
                    'insufficient-stock-tooltip' => 'Недостаточное количество в наличии',
                    'unit'                       => 'Единица',
                    'picked'                     => 'Собрано',
                ],

                'actions' => [
                    'open-product' => [
                        'tooltip' => 'Открыть товар',
                    ],
                ],

                'fields' => [
                    'product'        => 'Товар',
                    'final-location' => 'Конечное местоположение',
                    'description'    => 'Описание',
                    'scheduled-at'   => 'Запланировано на',
                    'deadline'       => 'Крайний срок',
                    'packaging'      => 'Фасовка',
                    'demand'         => 'Потребность',
                    'quantity'       => 'Количество',
                    'unit'           => 'Единица',
                    'picked'         => 'Собрано',

                    'lines' => [
                        'modal-heading'             => 'Управление движениями запасов',
                        'modal-submit-action-label' => 'Сохранить',
                        'add-line'                  => 'Добавить строку',

                        'actions' => [
                            'generate' => 'Сгенерировать серийные номера/партии',
                            'import'   => 'Импортировать серийные номера/партии',
                        ],

                        'fields' => [
                            'lot'                => 'Партия/серийный номер',
                            'pick-from'          => 'Взять из',
                            'location'           => 'Разместить в',
                            'package'            => 'Упаковка назначения',
                            'quantity'           => 'Количество',
                            'uom'                => 'Единица измерения',
                            'first-lot'          => 'Номер первой партии',
                            'quantity-per-lot'   => 'Количество в партии',
                            'quantity-received'  => 'Получено',
                            'keep-current-lines' => 'Сохранить текущие строки',
                            'serials'            => 'Партии/серийные номера',
                            'serials-helper'     => 'По одной партии/серийному номеру на строку.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Дополнительно',

                'fields' => [
                    'responsible'                  => 'Ответственный',
                    'shipping-policy'              => 'Политика отгрузки',
                    'shipping-policy-hint-tooltip' => 'Определяет, доставлять товары частями или все сразу.',
                    'scheduled-at'                 => 'Запланировано на',
                    'scheduled-at-hint-tooltip'    => 'Запланированное время обработки первой части отправки. Значение, заданное вручную, станет ожидаемой датой для всех движений запасов.',
                    'source-document'              => 'Исходный документ',
                    'source-document-hint-tooltip' => 'Номер документа',
                ],
            ],

            'note' => [
                'title' => 'Примечание',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'Избранное',
            'reference'       => 'Номер',
            'from'            => 'Откуда',
            'to'              => 'Куда',
            'contact'         => 'Контакт',
            'responsible'     => 'Ответственный',
            'scheduled-at'    => 'Запланировано на',
            'deadline'        => 'Крайний срок',
            'closed-at'       => 'Закрыто',
            'source-document' => 'Исходный документ',
            'operation-type'  => 'Тип операции',
            'company'         => 'Компания',
            'state'           => 'Статус',
            'deleted-at'      => 'Удалено',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'groups' => [
            'state'           => 'Статус',
            'source-document' => 'Исходный документ',
            'operation-type'  => 'Тип операции',
            'scheduled-at'    => 'Запланировано на',
            'created-at'      => 'Создано',
        ],

        'filters' => [
            'operation-type'       => 'Тип операции',
            'name'                 => 'Название',
            'state'                => 'Статус',
            'partner'              => 'Контакт',
            'responsible'          => 'Ответственный',
            'owner'                => 'Владелец',
            'source-location'      => 'Исходное местоположение',
            'destination-location' => 'Местоположение назначения',
            'deadline'             => 'Крайний срок',
            'scheduled-at'         => 'Запланировано на',
            'closed-at'            => 'Закрыто',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
            'company'              => 'Компания',
            'creator'              => 'Автор',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общая информация',
                'entries' => [
                    'contact'              => 'Контакт',
                    'operation-type'       => 'Тип операции',
                    'source-location'      => 'Исходное местоположение',
                    'destination-location' => 'Местоположение назначения',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Операции',
                'entries' => [
                    'product'        => 'Товар',
                    'final-location' => 'Конечное местоположение',
                    'description'    => 'Описание',
                    'scheduled-at'   => 'Запланировано на',
                    'deadline'       => 'Крайний срок',
                    'packaging'      => 'Фасовка',
                    'demand'         => 'Потребность',
                    'quantity'       => 'Количество',
                    'unit'           => 'Единица',
                    'picked'         => 'Собрано',
                ],
            ],
            'additional' => [
                'title'   => 'Дополнительная информация',
                'entries' => [
                    'responsible'     => 'Ответственный',
                    'shipping-policy' => 'Политика отгрузки',
                    'scheduled-at'    => 'Запланировано на',
                    'source-document' => 'Исходный документ',
                ],
            ],
            'note' => [
                'title' => 'Примечание',
            ],
        ],
    ],

    'tabs' => [
        'todo'        => 'К выполнению',
        'my'          => 'Мои перемещения',
        'starred'     => 'Избранные',
        'draft'       => 'Черновик',
        'waiting'     => 'Ожидание',
        'ready'       => 'Готово',
        'late'        => 'Просрочено',
        'done'        => 'Выполнено',
        'canceled'    => 'Отменено',
        'back-orders' => 'Недопоставки',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Предупреждение о точности единицы измерения',
            'body'  => 'Вы используете единицу измерения меньше той, в которой учитывается этот товар. Это может привести к ошибкам округления в зарезервированных количествах. Используйте наименьшую единицу измерения для оценки запасов или уменьшите точность округления базовой единицы.',
        ],
    ],
];
