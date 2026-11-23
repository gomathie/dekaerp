<?php

return [
    'title' => 'Контакты',

    'header-actions' => [
        'create' => [
            'label' => 'Создать Контакт',
        ],

        'import' => [
            'label' => 'Импорт от Excel',

            'modal' => [
                'heading'                  => 'Импорт контакты от spreadsheet',
                'description'              => 'Скачать пример файл, fill в one контакт за строка, затем загрузить это здесь. Вверх до :max контакты за загрузить.',
                'submit'                   => 'Импорт контакты',
                'download-template-csv'    => 'Скачать пример (CSV)',
                'download-template-excel'  => 'Скачать пример (Excel)',

                'file' => [
                    'label'  => 'Завершено файл',
                    'helper' => 'Excel (.xlsx,.xls) или CSV. Оставить heading строка как это. Обязательно колонка: :columns. Строки с problem reported от строка номер и rest всё ещё imported.',
                ],
            ],

            'notifications' => [
                'imported'        => ':count контакты imported',
                'skipped'         => ':count строки были skipped:',
                'nothing-imported' => 'Нет контакты были imported',
                'more-errors'     => '... и :count больше строки.',
                'unreadable'      => 'Тот файл could не быть чтение. Пожалуйста загрузить Excel или CSV файл.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'Individuals',
        'companies'   => 'Компании',
        'employees'   => 'Сотрудники',
        'customers'   => 'Клиенты',
        'vendors'     => 'Поставщики',
        'archived'    => 'В архиве',
    ],
];
