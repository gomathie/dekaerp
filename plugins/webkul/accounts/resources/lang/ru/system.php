<?php

return [
    'move' => [
        'no-journal-found-title' => 'Учёт не настроен',
        'no-journal-found'       => 'В компании :company не найден журнал ни одного из типов: :types. Сначала настройте план счетов для этой компании.',
    ],

    'tax-formula' => [
        'empty'                => 'Формула не может быть пустой.',
        'invalid-character'    => 'Формула содержит недопустимый символ: :character',
        'unexpected-token'     => 'Формула содержит неожиданный элемент: :token',
        'unexpected-end'       => 'Формула неожиданно обрывается.',
        'unclosed-parenthesis' => 'В формуле есть незакрытая скобка.',
        'unknown-variable'     => 'Неизвестная переменная «:variable». Доступны только переменные: :variables',
        'unknown-function'     => 'Неизвестная функция «:function». Доступны только функции: :functions',
    ],
];
