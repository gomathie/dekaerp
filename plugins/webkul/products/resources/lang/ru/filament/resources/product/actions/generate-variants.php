<?php

return [
    'label'        => 'Generate Variants',
    'notification' => [
        'empty' => [
            'title' => 'Нет attributes найдено',
            'body'  => 'Пожалуйста добавить attributes до generate variants.',
        ],

        'success' => [
            'title' => 'Variants generated successfully',
            'body'  => 'Все товар variants есть generated.',
        ],

        'error' => [
            'title' => 'Ошибка generating variants',
            'body'  => 'ошибка occurred пока generating товар variants.',
        ],
    ],
];
