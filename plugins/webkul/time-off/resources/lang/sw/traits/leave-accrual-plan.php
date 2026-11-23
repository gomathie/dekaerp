<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'Accrual Kiasi',
            'accrual-value-type'          => 'Accrual Thamani Aina',
            'accrual-frequency'           => 'Accrual Frequency',
            'accrual-day'                 => 'Accrual Siku',
            'day-of-month'                => 'Siku ya Mwezi',
            'first-day-of-month'          => 'Kwanza Siku ya Mwezi',
            'second-day-of-month'         => 'Second Siku ya Mwezi',
            'first-period-month'          => 'Kwanza Kipindi Mwezi',
            'first-period-day'            => 'Kwanza Kipindi Siku',
            'second-period-month'         => 'Second Kipindi Mwezi',
            'second-period-day'           => 'Second Kipindi Siku',
            'first-period-year'           => 'Kwanza Kipindi Mwaka',
            'cap-accrued-time'            => 'Cap accrued time',
            'days'                        => 'Siku',
            'start-count'                 => 'Anza Idadi',
            'start-type'                  => 'Anza Aina',
            'action-with-unused-accruals' => 'Kitendo na Unused Accruals',
            'milestone-cap'               => 'Hatua kuu Cap',
            'maximum-leave-yearly'        => 'Maximum Likizo Kila mwaka',
            'accrual-validity'            => 'Accrual Validity',
            'accrual-validity-count'      => 'Accrual Validity Idadi',
            'accrual-validity-type'       => 'Accrual Validity Aina',
            'advanced-accrual-settings'   => 'Advanced Accrual Mipangilio',
            'after-allocation-start'      => 'Baada ya Allocation Anza Tarehe',
            'to-date'                     => 'Hadi Tarehe',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'Accrual Kiasi',
            'accrual-value-type' => 'Accrual Thamani Aina',
            'frequency'          => 'Frequency',
            'maximum-leave-days' => 'Maximum Likizo Siku',
        ],

        'groups' => [
            'accrual-amount'       => 'Accrual Kiasi',
            'accrual-value-type'   => 'Accrual Thamani Aina',
            'frequency'            => 'Frequency',
            'maximum-leave-days'   => 'Maximum Likizo Siku',
        ],

        'filters' => [
            'accrual-frequency'           => 'Accrual Frequency',
            'start-type'                  => 'Anza Aina',
            'cap-accrued-time'            => 'Cap Accrued Time',
            'action-with-unused-accruals' => 'Kitendo Na Unused Accruals',
            'accrual-amount'              => 'Accrual Kiasi',
            'accrual-frequency'           => 'Accrual Frequency',
            'start-type'                  => 'Anza Aina',
            'created-at'                  => 'Imeundwa',
            'updated-at'                  => 'Imesasishwa',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Mpya Likizo Accrual Plan',

                'notification' => [
                    'title' => 'Likizo accrual plan imeundwa',
                    'body'  => 'likizo accrual plan ina imeundwa successfully.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Likizo accrual plan imesasishwa',
                    'body'  => 'likizo accrual plan ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Likizo accrual plan imefutwa',
                    'body'  => 'likizo accrual plan ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Likizo accrual plans imefutwa',
                    'body'  => 'likizo accrual plans ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'Accrual Kiasi',
            'accrual-value-type'          => 'Accrual Thamani Aina',
            'accrual-frequency'           => 'Accrual Frequency',
            'accrual-day'                 => 'Accrual Siku',
            'day-of-month'                => 'Siku ya Mwezi',
            'first-day-of-month'          => 'Kwanza Siku ya Mwezi',
            'second-day-of-month'         => 'Second Siku ya Mwezi',
            'first-period-month'          => 'Kwanza Kipindi Mwezi',
            'first-period-day'            => 'Kwanza Kipindi Siku',
            'second-period-month'         => 'Second Kipindi Mwezi',
            'second-period-day'           => 'Second Kipindi Siku',
            'first-period-year'           => 'Kwanza Kipindi Mwaka',
            'cap-accrued-time'            => 'Cap accrued time',
            'days'                        => 'Siku',
            'start-count'                 => 'Anza Idadi',
            'start-type'                  => 'Anza Aina',
            'action-with-unused-accruals' => 'Kitendo na Unused Accruals',
            'milestone-cap'               => 'Hatua kuu Cap',
            'maximum-leave-yearly'        => 'Maximum Likizo Kila mwaka',
            'accrual-validity'            => 'Accrual Validity',
            'accrual-validity-count'      => 'Accrual Validity Idadi',
            'accrual-validity-type'       => 'Accrual Validity Aina',
            'advanced-accrual-settings'   => 'Advanced Accrual Mipangilio',
            'after-allocation-start'      => 'Baada ya Allocation Anza Tarehe',
        ],
    ],
];
