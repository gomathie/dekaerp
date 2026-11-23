<?php

return [
    'errors' => [
        'empty'           => 'файл есть нет строки.',
        'no-columns'      => 'Нет recognised колонки были найдено в heading строка. Ожидается колонки: :columns.',
        'missing-name'    => 'файл нужно есть "название" колонка.',
        'max-rows'        => 'Только первый :max строки были imported. Split файл и загрузить rest.',
        'row'             => 'Строка :row: :reason',
        'formula'         => 'Строка :row: ":column" значение starts с character spreadsheet reads как formula. Убрать это и загрузить снова.',
        'duplicate-email' => 'Строка :row: контакт с эл. почта :email уже существует.',
        'unknown-country' => 'Строка :row: нет страна совпадения ":country".',
        'unknown-state'   => 'Строка :row: нет состояние совпадения ":state".',
        'unknown-parent'  => 'Строка :row: нет existing контакт совпадения компания ":parent".',
    ],
];
