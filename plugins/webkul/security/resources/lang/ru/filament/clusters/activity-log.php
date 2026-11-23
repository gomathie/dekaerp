<?php

return [
    'title'           => 'Активность log',
    'navigation'      => 'Активность log',
    'group'           => 'Общее',
    'system'          => 'Система',
    'export'          => 'Экспорт',
    'export-complete' => 'Ваш активность log экспорт ready: :count строки.',

    'columns' => [
        'when'    => 'Когда',
        'who'     => 'Кто',
        'record'  => 'Запись',
        'action'  => 'Действие',
        'changes' => 'Changes',
    ],

    'filters' => [
        'record-type' => 'Запись тип',
        'from'        => 'От',
        'until'       => 'До',
    ],

    'events' => [
        'created'      => 'Создано',
        'updated'      => 'Изменено',
        'deleted'      => 'Удалено',
        'soft_deleted' => 'В архиве',
        'hard_deleted' => 'Удалено',
        'restored'     => 'Restored',
        'reset'        => 'Сбросить до черновик',
        'cancelled'    => 'Отменён',
    ],
];
