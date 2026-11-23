<?php

return [

    'label' => 'Query builder',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Groups',

            'group' => [
                'label' => 'Kundi',
            ],

            'block' => [
                'label' => 'Au condition',
                'or' => 'Au',
            ],

        ],

        'rules' => [

            'label' => 'Rules',

            'item' => [
                'and' => 'Na',
            ],

        ],

    ],

    'no_rules' => '(Hakuna rules)',

    'max_rules_reached_tooltip' => 'Wewe ina reached maximum ya :count rules.',

    'item_separators' => [
        'and' => 'Na',
        'or' => 'Au',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Ni filled',
                'inverse' => 'Ni blank',
            ],

            'summary' => [
                'direct' => ':attribute ni filled',
                'inverse' => ':attribute ni blank',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Ni ndiyo',
                    'inverse' => 'Ni hapana',
                ],

                'summary' => [
                    'direct' => ':attribute ni ndiyo',
                    'inverse' => ':attribute ni hapana',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Ni baada ya',
                    'inverse' => 'Ni si baada ya',
                ],

                'summary' => [
                    'direct' => ':attribute ni baada ya :date',
                    'inverse' => ':attribute ni si baada ya :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Ni kabla ya',
                    'inverse' => 'Ni si kabla ya',
                ],

                'summary' => [
                    'direct' => ':attribute ni kabla ya :date',
                    'inverse' => ':attribute ni si kabla ya :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Ni tarehe',
                    'inverse' => 'Ni si tarehe',
                ],

                'summary' => [
                    'direct' => ':attribute ni :date',
                    'inverse' => ':attribute ni si :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Ni mwezi',
                    'inverse' => 'Ni si mwezi',
                ],

                'summary' => [
                    'direct' => ':attribute ni :month',
                    'inverse' => ':attribute ni si :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Ni mwaka',
                    'inverse' => 'Ni si mwaka',
                ],

                'summary' => [
                    'direct' => ':attribute ni :year',
                    'inverse' => ':attribute ni si :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Seconds',
                'minute' => 'Dakika',
                'hour' => 'Saa',
                'day' => 'Siku',
                'week' => 'Weeks',
                'month' => 'Months',
                'quarter' => 'Quarters',
                'year' => 'Miaka',
            ],

            'presets' => [
                'past_decade' => 'Zilizopita decade',
                'past_5_years' => 'Zilizopita 5 miaka',
                'past_2_years' => 'Zilizopita 2 miaka',
                'past_year' => 'Zilizopita mwaka',
                'past_6_months' => 'Zilizopita 6 months',
                'past_quarter' => 'Zilizopita robo',
                'past_month' => 'Zilizopita mwezi',
                'past_2_weeks' => 'Zilizopita 2 weeks',
                'past_week' => 'Zilizopita wiki',
                'past_hour' => 'Zilizopita saa',
                'past_minute' => 'Zilizopita dakika',
                'this_decade' => 'Hii decade',
                'this_year' => 'Hii mwaka',
                'this_quarter' => 'Hii robo',
                'this_month' => 'Hii mwezi',
                'today' => 'Leo',
                'this_hour' => 'Hii saa',
                'this_minute' => 'Hii dakika',
                'next_minute' => 'Ifuatayo dakika',
                'next_hour' => 'Ifuatayo saa',
                'next_week' => 'Ifuatayo wiki',
                'next_2_weeks' => 'Ifuatayo 2 weeks',
                'next_month' => 'Ifuatayo mwezi',
                'next_quarter' => 'Ifuatayo robo',
                'next_6_months' => 'Ifuatayo 6 months',
                'next_year' => 'Ifuatayo mwaka',
                'next_2_years' => 'Ifuatayo 2 miaka',
                'next_5_years' => 'Ifuatayo 5 miaka',
                'next_decade' => 'Ifuatayo decade',
                'custom' => 'Maalum',
            ],

            'form' => [

                'date' => [
                    'label' => 'Tarehe',
                ],

                'month' => [
                    'label' => 'Mwezi',
                ],

                'year' => [
                    'label' => 'Mwaka',
                ],

                'mode' => [

                    'label' => 'Tarehe aina',

                    'options' => [
                        'absolute' => 'Specific tarehe',
                        'relative' => 'Rolling window',
                    ],

                ],

                'preset' => [
                    'label' => 'Time kipindi',
                ],

                'relative_value' => [
                    'label' => 'Jinsi nyingi',
                ],

                'relative_unit' => [
                    'label' => 'Time uniti',
                ],

                'tense' => [

                    'label' => 'Tense',

                    'options' => [
                        'past' => 'Zilizopita',
                        'future' => 'Zijazo',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Equals',
                    'inverse' => 'si equal',
                ],

                'summary' => [
                    'direct' => ':attribute equals :number',
                    'inverse' => ':attribute si equal :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Ni maximum',
                    'inverse' => 'Ni greater kuliko',
                ],

                'summary' => [
                    'direct' => ':attribute ni maximum :number',
                    'inverse' => ':attribute ni greater kuliko :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Ni minimum',
                    'inverse' => 'Ni chache kuliko',
                ],

                'summary' => [
                    'direct' => ':attribute ni minimum :number',
                    'inverse' => ':attribute ni chache kuliko :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Average',
                    'summary' => 'Average :attribute',
                ],

                'max' => [
                    'label' => 'Max',
                    'summary' => 'Max :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Sum',
                    'summary' => 'Sum ya :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Aggregate',
                ],

                'number' => [
                    'label' => 'Nambari',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ina',
                    'inverse' => 'si ina',
                ],

                'summary' => [
                    'direct' => 'Ina :count :relationship',
                    'inverse' => 'si ina :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ina maximum',
                    'inverse' => 'Ina zaidi kuliko',
                ],

                'summary' => [
                    'direct' => 'Ina maximum :count :relationship',
                    'inverse' => 'Ina zaidi kuliko :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ina minimum',
                    'inverse' => 'Ina chache kuliko',
                ],

                'summary' => [
                    'direct' => 'Ina minimum :count :relationship',
                    'inverse' => 'Ina chache kuliko :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Ni tupu',
                    'inverse' => 'Ni si tupu',
                ],

                'summary' => [
                    'direct' => ':relationship ni tupu',
                    'inverse' => ':relationship ni si tupu',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Ni',
                        'inverse' => 'Ni si',
                    ],

                    'multiple' => [
                        'direct' => 'Contains',
                        'inverse' => 'si contain',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship ni :values',
                        'inverse' => ':relationship ni si :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contains :values',
                        'inverse' => ':relationship si contain :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' au ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Thamani',
                    ],

                    'values' => [
                        'label' => 'Values',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Idadi',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Ni',
                    'inverse' => 'Ni si',
                ],

                'summary' => [
                    'direct' => ':attribute ni :values',
                    'inverse' => ':attribute ni si :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' au ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Thamani',
                    ],

                    'values' => [
                        'label' => 'Values',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Contains',
                    'inverse' => 'si contain',
                ],

                'summary' => [
                    'direct' => ':attribute contains :text',
                    'inverse' => ':attribute si contain :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Ends na',
                    'inverse' => 'si mwisho na',
                ],

                'summary' => [
                    'direct' => ':attribute ends na :text',
                    'inverse' => ':attribute si mwisho na :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Equals',
                    'inverse' => 'si equal',
                ],

                'summary' => [
                    'direct' => ':attribute equals :text',
                    'inverse' => ':attribute si equal :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Starts na',
                    'inverse' => 'si anza na',
                ],

                'summary' => [
                    'direct' => ':attribute starts na :text',
                    'inverse' => ':attribute si anza na :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Text',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Ongeza rule',
        ],

        'add_rule_group' => [
            'label' => 'Ongeza Au',
        ],

    ],

];
