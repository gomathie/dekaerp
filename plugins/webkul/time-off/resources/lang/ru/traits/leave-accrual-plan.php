<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'Размер начисления',
            'accrual-value-type'          => 'Единица начисления',
            'accrual-frequency'           => 'Частота начисления',
            'accrual-day'                 => 'День начисления',
            'day-of-month'                => 'День месяца',
            'first-day-of-month'          => 'Первый день месяца',
            'second-day-of-month'         => 'Второй день месяца',
            'first-period-month'          => 'Месяц первого периода',
            'first-period-day'            => 'День первого периода',
            'second-period-month'         => 'Месяц второго периода',
            'second-period-day'           => 'День второго периода',
            'first-period-year'           => 'Год первого периода',
            'cap-accrued-time'            => 'Ограничить начисленное время',
            'days'                        => 'Дни',
            'start-count'                 => 'Начало отсчёта',
            'start-type'                  => 'Единица отсчёта',
            'action-with-unused-accruals' => 'Действие с неиспользованными начислениями',
            'milestone-cap'               => 'Лимит этапа',
            'maximum-leave-yearly'        => 'Максимум отпуска в год',
            'accrual-validity'            => 'Срок действия начисления',
            'accrual-validity-count'      => 'Длительность срока действия',
            'accrual-validity-type'       => 'Единица срока действия',
            'advanced-accrual-settings'   => 'Расширенные настройки начисления',
            'after-allocation-start'      => 'После даты начала распределения',
            'to-date'                     => 'По дату',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'Размер начисления',
            'accrual-value-type' => 'Единица начисления',
            'frequency'          => 'Частота',
            'maximum-leave-days' => 'Максимум дней отпуска',
        ],

        'groups' => [
            'accrual-amount'       => 'Размер начисления',
            'accrual-value-type'   => 'Единица начисления',
            'frequency'            => 'Частота',
            'maximum-leave-days'   => 'Максимум дней отпуска',
        ],

        'filters' => [
            'accrual-frequency'           => 'Частота начисления',
            'start-type'                  => 'Единица отсчёта',
            'cap-accrued-time'            => 'Ограничить начисленное время',
            'action-with-unused-accruals' => 'Действие с неиспользованными начислениями',
            'accrual-amount'              => 'Размер начисления',
            'accrual-frequency'           => 'Частота начисления',
            'start-type'                  => 'Единица отсчёта',
            'created-at'                  => 'Создано',
            'updated-at'                  => 'Обновлено',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Новый план начисления отпуска',

                'notification' => [
                    'title' => 'План начисления отпуска создан',
                    'body'  => 'План начисления отпуска успешно создан.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'План начисления отпуска обновлён',
                    'body'  => 'План начисления отпуска успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'План начисления отпуска удалён',
                    'body'  => 'План начисления отпуска успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Планы начисления отпуска удалены',
                    'body'  => 'Планы начисления отпуска успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'Размер начисления',
            'accrual-value-type'          => 'Единица начисления',
            'accrual-frequency'           => 'Частота начисления',
            'accrual-day'                 => 'День начисления',
            'day-of-month'                => 'День месяца',
            'first-day-of-month'          => 'Первый день месяца',
            'second-day-of-month'         => 'Второй день месяца',
            'first-period-month'          => 'Месяц первого периода',
            'first-period-day'            => 'День первого периода',
            'second-period-month'         => 'Месяц второго периода',
            'second-period-day'           => 'День второго периода',
            'first-period-year'           => 'Год первого периода',
            'cap-accrued-time'            => 'Ограничить начисленное время',
            'days'                        => 'Дни',
            'start-count'                 => 'Начало отсчёта',
            'start-type'                  => 'Единица отсчёта',
            'action-with-unused-accruals' => 'Действие с неиспользованными начислениями',
            'milestone-cap'               => 'Лимит этапа',
            'maximum-leave-yearly'        => 'Максимум отпуска в год',
            'accrual-validity'            => 'Срок действия начисления',
            'accrual-validity-count'      => 'Длительность срока действия',
            'accrual-validity-type'       => 'Единица срока действия',
            'advanced-accrual-settings'   => 'Расширенные настройки начисления',
            'after-allocation-start'      => 'После даты начала распределения',
        ],
    ],
];
