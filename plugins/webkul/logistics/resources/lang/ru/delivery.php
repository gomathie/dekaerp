<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'Mark picked вверх',
            'notification'         => 'Отправка marked как picked вверх',
            'transit-label'        => 'Начать transit',
            'transit-notification' => 'Отправка marked в transit',
        ],
        'deliver' => [
            'label'                         => 'Deliver отправка',
            'notification'                  => 'Отправка delivered',
            'stop-notification'             => 'Доставка остановка recorded',
            'out-for-delivery-label'        => 'Mark вне для доставка',
            'out-for-delivery-notification' => 'Отправка marked вне для доставка',
            'fields'                        => [
                'capture-pod'    => 'Capture proof доставка',
                'stop'           => 'Доставка остановка',
                'recipient-name' => 'Получатель название',
                'received-at'    => 'Получено в',
                'reference'      => 'Ссылка',
                'notes'          => 'Заметки',
                'photo'          => 'Доставка photo',
                'signature'      => 'Получатель signature',
            ],
        ],
        'fail' => [
            'label'               => 'Запись не удалось доставка',
            'reason'              => 'Причина',
            'notification'        => 'Не удалось доставка recorded',
            'resolve-label'       => 'Resolve не удалось доставка',
            'resolution'          => 'Далее step',
            'retry-notification'  => 'Отправка ready для another доставка attempt',
            'return-notification' => 'Отправка marked как returned',
            'resolutions'         => [
                'retry'  => 'Повторить доставка',
                'return' => 'Mark returned',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'Proof доставка',
        'columns' => [
            'recipient'    => 'Получатель',
            'received-at'  => 'Получено в',
            'stop'         => 'Остановка',
            'captured-via' => 'Captured через',
            'photo'        => 'Photo',
            'signature'    => 'Signature',
        ],
    ],
    'validation' => [
        'pod-required'        => 'Proof доставка обязательно до этот отправка можно быть delivered.',
        'stop-required'       => 'Выбрать доставка остановка этот proof belongs до.',
        'stop-unavailable'    => 'выбрано доставка остановка нет longer доступно.',
        'upload-failed'       => 'proof файл could не быть stored. Пожалуйста попробуйте снова.',
        'resolution-required' => 'Выбрать whether до повторить или return не удалось доставка.',
    ],
];
