<?php

return [
    'heading' => [
        'title' => 'Заявки на отпуск',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'Изменить',
            'duration-display'              => ':count рабочий день|:count рабочих дня|:count рабочих дней',
            'duration-display-with-weekend' => ':count рабочий день (+ выходных: :weekend)|:count рабочих дня (+ выходных: :weekend)|:count рабочих дней (+ выходных: :weekend)',

            'notification' => [
                'title' => 'Отпуск обновлён',
                'body'  => 'Ваша заявка на отпуск успешно обновлена.',
            ],
        ],

        'delete' => [
            'title' => 'Удалить',
        ],
    ],

    'config' => [
        'button-text' => [
            'today' => 'Сегодня',
            'month' => 'Месяц',
            'week'  => 'Неделя',
            'list'  => 'Список',
        ],
    ],

    'view-action' => [
        'title'       => 'Просмотр',
        'description' => 'Просмотр заявки на отпуск',
    ],

    'notifications' => [
        'employee-not-found' => [
            'title' => 'Сотрудник не найден',
            'body'  => 'Перед подачей заявки на отпуск добавьте сотрудника в свой профиль.',
        ],

        'error' => [
            'title' => 'Что-то пошло не так',
            'body'  => 'Не удалось обработать вашу заявку на отпуск. Попробуйте ещё раз.',
        ],
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Новый отпуск',
            'description' => 'Создать заявку на отпуск',

            'notification' => [
                'title' => 'Отпуск создан',
                'body'  => 'Заявка на отпуск успешно создана.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Сотрудник не найден',
                    'body'  => 'Перед созданием заявки на отпуск добавьте сотрудника в свой профиль.',
                ],
            ],

            'success' => [
                'notification' => [
                    'title' => 'Отпуск создан',
                    'body'  => 'Ваша заявка на отпуск успешно создана.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'Заявка на отпуск',
        'description' => 'Создайте или измените заявку на отпуск, указав следующие данные:',

        'fields' => [
            'time-off-type'             => 'Тип отпуска',
            'time-off-type-placeholder' => 'Выберите тип отпуска',
            'time-off-type-helper'      => 'Выберите тип запрашиваемого отпуска.',
            'request-date-from'         => 'Запрошено с',
            'request-date-to'           => 'Запрошено по',
            'period'                    => 'Период',
            'half-day'                  => 'Полдня',
            'half-day-helper'           => 'Включите для отпуска на полдня.',
            'requested-days'            => 'Запрошено (дни/часы)',
            'description'               => 'Описание',
            'description-placeholder'   => 'Описание не указано',
            'description-helper'        => 'Кратко опишите заявку на отпуск.',
            'duration'                  => 'Продолжительность',
            'please-select-dates'       => 'Выберите даты начала и окончания.',
        ],
    ],

    'infolist' => [
        'title'       => 'Сведения об отпуске',
        'description' => 'Сведения о вашей заявке на отпуск:',
        'entries'     => [
            'time-off-type'           => 'Тип отпуска',
            'request-date-from'       => 'Запрошено с',
            'request-date-to'         => 'Запрошено по',
            'description'             => 'Описание',
            'description-placeholder' => 'Описание не указано',
            'duration'                => 'Продолжительность',
            'status'                  => 'Статус',
        ],
    ],

    'events' => [
        'title' => ':name — :status: :days дн.',
    ],
];
