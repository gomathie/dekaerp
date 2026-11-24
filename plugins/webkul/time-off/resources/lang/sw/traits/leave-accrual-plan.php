<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'Kiasi cha ulimbikizaji',
            'accrual-value-type'          => 'Aina ya thamani ya ulimbikizaji',
            'accrual-frequency'           => 'Marudio ya ulimbikizaji',
            'accrual-day'                 => 'Siku ya ulimbikizaji',
            'day-of-month'                => 'Siku ya mwezi',
            'first-day-of-month'          => 'Siku ya kwanza ya mwezi',
            'second-day-of-month'         => 'Siku ya pili ya mwezi',
            'first-period-month'          => 'Mwezi wa kipindi cha kwanza',
            'first-period-day'            => 'Siku ya kipindi cha kwanza',
            'second-period-month'         => 'Mwezi wa kipindi cha pili',
            'second-period-day'           => 'Siku ya kipindi cha pili',
            'first-period-year'           => 'Mwaka wa kipindi cha kwanza',
            'cap-accrued-time'            => 'Weka kikomo cha muda uliolimbikizwa',
            'days'                        => 'Siku',
            'start-count'                 => 'Hesabu ya kuanza',
            'start-type'                  => 'Aina ya kuanza',
            'action-with-unused-accruals' => 'Kitendo kwa ulimbikizaji usiotumika',
            'milestone-cap'               => 'Kikomo cha hatua',
            'maximum-leave-yearly'        => 'Kiwango cha juu cha likizo kwa mwaka',
            'accrual-validity'            => 'Uhalali wa ulimbikizaji',
            'accrual-validity-count'      => 'Idadi ya uhalali wa ulimbikizaji',
            'accrual-validity-type'       => 'Aina ya uhalali wa ulimbikizaji',
            'advanced-accrual-settings'   => 'Mipangilio ya kina ya ulimbikizaji',
            'after-allocation-start'      => 'Baada ya tarehe ya kuanza kwa mgao',
            'to-date'                     => 'Hadi tarehe',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'Kiasi cha ulimbikizaji',
            'accrual-value-type' => 'Aina ya thamani ya ulimbikizaji',
            'frequency'          => 'Marudio',
            'maximum-leave-days' => 'Kiwango cha juu cha siku za likizo',
        ],

        'groups' => [
            'accrual-amount'       => 'Kiasi cha ulimbikizaji',
            'accrual-value-type'   => 'Aina ya thamani ya ulimbikizaji',
            'frequency'            => 'Marudio',
            'maximum-leave-days'   => 'Kiwango cha juu cha siku za likizo',
        ],

        'filters' => [
            'accrual-frequency'           => 'Marudio ya ulimbikizaji',
            'start-type'                  => 'Aina ya kuanza',
            'cap-accrued-time'            => 'Weka kikomo cha muda uliolimbikizwa',
            'action-with-unused-accruals' => 'Kitendo kwa ulimbikizaji usiotumika',
            'accrual-amount'              => 'Kiasi cha ulimbikizaji',
            'accrual-frequency'           => 'Marudio ya ulimbikizaji',
            'start-type'                  => 'Aina ya kuanza',
            'created-at'                  => 'Iliundwa',
            'updated-at'                  => 'Ilisasishwa',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'Mpango mpya wa ulimbikizaji wa likizo',

                'notification' => [
                    'title' => 'Mpango wa ulimbikizaji wa likizo umeundwa',
                    'body'  => 'Mpango wa ulimbikizaji wa likizo umeundwa kwa mafanikio.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mpango wa ulimbikizaji wa likizo umesasishwa',
                    'body'  => 'Mpango wa ulimbikizaji wa likizo umesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mpango wa ulimbikizaji wa likizo umefutwa',
                    'body'  => 'Mpango wa ulimbikizaji wa likizo umefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'Mipango ya ulimbikizaji wa likizo imefutwa',
                    'body'  => 'Mipango ya ulimbikizaji wa likizo imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'Kiasi cha ulimbikizaji',
            'accrual-value-type'          => 'Aina ya thamani ya ulimbikizaji',
            'accrual-frequency'           => 'Marudio ya ulimbikizaji',
            'accrual-day'                 => 'Siku ya ulimbikizaji',
            'day-of-month'                => 'Siku ya mwezi',
            'first-day-of-month'          => 'Siku ya kwanza ya mwezi',
            'second-day-of-month'         => 'Siku ya pili ya mwezi',
            'first-period-month'          => 'Mwezi wa kipindi cha kwanza',
            'first-period-day'            => 'Siku ya kipindi cha kwanza',
            'second-period-month'         => 'Mwezi wa kipindi cha pili',
            'second-period-day'           => 'Siku ya kipindi cha pili',
            'first-period-year'           => 'Mwaka wa kipindi cha kwanza',
            'cap-accrued-time'            => 'Weka kikomo cha muda uliolimbikizwa',
            'days'                        => 'Siku',
            'start-count'                 => 'Hesabu ya kuanza',
            'start-type'                  => 'Aina ya kuanza',
            'action-with-unused-accruals' => 'Kitendo kwa ulimbikizaji usiotumika',
            'milestone-cap'               => 'Kikomo cha hatua',
            'maximum-leave-yearly'        => 'Kiwango cha juu cha likizo kwa mwaka',
            'accrual-validity'            => 'Uhalali wa ulimbikizaji',
            'accrual-validity-count'      => 'Idadi ya uhalali wa ulimbikizaji',
            'accrual-validity-type'       => 'Aina ya uhalali wa ulimbikizaji',
            'advanced-accrual-settings'   => 'Mipangilio ya kina ya ulimbikizaji',
            'after-allocation-start'      => 'Baada ya tarehe ya kuanza kwa mgao',
        ],
    ],
];
