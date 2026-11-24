<?php

return [
    'title'           => 'Журнал действий',
    'navigation'      => 'Журнал действий',
    'group'           => 'Общее',
    'system'          => 'Система',
    'export'          => 'Экспорт',
    'export-complete' => 'Экспорт журнала действий готов, строк: :count.',

    'columns' => [
        'when'    => 'Когда',
        'who'     => 'Кто',
        'record'  => 'Запись',
        'action'  => 'Действие',
        'changes' => 'Изменения',
    ],

    'filters' => [
        'record-type' => 'Тип записи',
        'from'        => 'С',
        'until'       => 'По',
    ],

    'events' => [
        'created'      => 'Создано',
        'updated'      => 'Изменено',
        'deleted'      => 'Удалено',
        'soft_deleted' => 'Архивировано',
        'hard_deleted' => 'Удалено',
        'restored'     => 'Восстановлено',
        'reset'        => 'Возвращено в черновик',
        'cancelled'    => 'Отменено',
    ],
];
