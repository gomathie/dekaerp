<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'Accrual Сумма',
            'accrual-value-type'          => 'Accrual Значение Тип',
            'accrual-frequency'           => 'Accrual Frequency',
            'accrual-day'                 => 'Accrual День',
            'day-of-month'                => 'День Месяц',
            'first-day-of-month'          => 'Первый День Месяц',
            'second-day-of-month'         => 'Second День Месяц',
            'first-period-month'          => 'Первый Период Месяц',
            'first-period-day'            => 'Первый Период День',
            'second-period-month'         => 'Second Период Месяц',
            'second-period-day'           => 'Second Период День',
            'first-period-year'           => 'Первый Период Год',
            'cap-accrued-time'            => 'Cap accrued time',
            'days'                        => 'Дни',
            'start-count'                 => 'Начать Количество',
            'start-type'                  => 'Начать Тип',
            'action-with-unused-accruals' => 'Действие с Unused Accruals',
            'milestone-cap'               => 'Веха Cap',
            'maximum-leave-yearly'        => 'Maximum Отпуск Ежегодно',
            'accrual-validity'            => 'Accrual Validity',
            'accrual-validity-count'      => 'Accrual Validity Количество',
            'accrual-validity-type'       => 'Accrual Validity Тип',
            'advanced-accrual-settings'   => 'Advanced Accrual Настройки',
            'after-allocation-start'      => 'После Allocation Начать Дата',
            'to-date'                     => 'До Дата',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'Accrual Сумма',
            'accrual-value-type' => 'Accrual Значение Тип',
            'frequency'          => 'Frequency',
            'maximum-leave-days' => 'Maximum Отпуск Дни',
        ],

        'groups' => [
            'accrual-amount'       => 'Accrual Сумма',
            'accrual-value-type'   => 'Accrual Значение Тип',
            'frequency'            => 'Frequency',
            'maximum-leave-days'   => 'Maximum Отпуск Дни',
        ],

        'filters' => [
            'accrual-frequency'           => 'Accrual Frequency',
            'start-type'                  => 'Начать Тип',
            'cap-accrued-time'            => 'Cap Accrued Time',
            'action-with-unused-accruals' => 'Действие С Unused Accruals',
            'accrual-amount'              => 'Accrual Сумма',
            'accrual-frequency'           => 'Accrual Frequency',
            'start-type'                  => 'Начать Тип',
            'created-at'                  => 'Создано',
            'updated-at'                  => 'Обновлено',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Новое Отпуск Accrual Plan',

                'notification' => [
                    'title' => 'Отпуск accrual plan создано',
                    'body'  => 'отпуск accrual plan есть создано successfully.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Отпуск accrual plan обновлено',
                    'body'  => 'отпуск accrual plan есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отпуск accrual plan удалено',
                    'body'  => 'отпуск accrual plan есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Отпуск accrual plans удалено',
                    'body'  => 'отпуск accrual plans есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'Accrual Сумма',
            'accrual-value-type'          => 'Accrual Значение Тип',
            'accrual-frequency'           => 'Accrual Frequency',
            'accrual-day'                 => 'Accrual День',
            'day-of-month'                => 'День Месяц',
            'first-day-of-month'          => 'Первый День Месяц',
            'second-day-of-month'         => 'Second День Месяц',
            'first-period-month'          => 'Первый Период Месяц',
            'first-period-day'            => 'Первый Период День',
            'second-period-month'         => 'Second Период Месяц',
            'second-period-day'           => 'Second Период День',
            'first-period-year'           => 'Первый Период Год',
            'cap-accrued-time'            => 'Cap accrued time',
            'days'                        => 'Дни',
            'start-count'                 => 'Начать Количество',
            'start-type'                  => 'Начать Тип',
            'action-with-unused-accruals' => 'Действие с Unused Accruals',
            'milestone-cap'               => 'Веха Cap',
            'maximum-leave-yearly'        => 'Maximum Отпуск Ежегодно',
            'accrual-validity'            => 'Accrual Validity',
            'accrual-validity-count'      => 'Accrual Validity Количество',
            'accrual-validity-type'       => 'Accrual Validity Тип',
            'advanced-accrual-settings'   => 'Advanced Accrual Настройки',
            'after-allocation-start'      => 'После Allocation Начать Дата',
        ],
    ],
];
