<?php

return [
    'title'                  => 'Подтвердить доставка',
    'shipment'               => 'Отправка',
    'stop'                   => 'Остановка',
    'confidential-shipment'  => 'Confidential отправка — cargo подробности не disclosed.',

    'ttl' => [
        '4'  => '4 часы',
        '8'  => '8 часы',
        '16' => '16 часы',
        '24' => '24 часы (1 день)',
        '48' => '48 часы (2 дни)',
    ],

    'form' => [
        'recipient-name'      => 'Получено от',
        'recipient-name-help' => 'название person кто took доставка.',
        'recipient-id'        => 'Получатель Ид.',
        'recipient-id-help'   => 'Ид. или ссылка от person receiving, как ваш компания requires.',
        'photo'               => 'Photo',
        'photo-help'          => 'photo delivered goods, door, или signed накладная.',
        'signature'           => 'Signature',
        'signature-help'      => 'Ask получатель до войти box.',
        'signature-clear'     => 'Очистить',
        'notes'               => 'Заметки',
        'location'            => 'Attach мой место',
        'location-attached'   => 'Место attached.',
        'location-failed'     => 'Место недоступно. Вы можно всё ещё отправить.',
        'submit'              => 'Подтвердить доставка',
        'submitting'          => 'Confirming…',
    ],

    'done' => [
        'title'   => 'Доставка подтверждён',
        'message' => 'Thank вы. доставка есть recorded и этот link сейчас закрыт.',
    ],

    'expired' => [
        'title'   => 'Этот link нет longer действительно',
        'message' => 'Это май есть expired, уже используется, или withdrawn. Ask office для новое link.',
    ],

    'actions' => [
        'revoke-pod-link' => [
            'label'        => 'Отмена POD link',
            'heading'      => 'Отмена proof доставка link?',
            'description'  => 'Любой link уже отправлено для этот отправка остановки working immediately. Использовать этот если link went до wrong номер.',
            'notification' => '{0}был нет live link до отмена.|{1}Link отменён.|[2,*]:count links отменён.',
        ],
        'send-pod-link' => [
            'label'        => 'POD link',
            'heading'      => 'One-time proof доставка link',
            'description'  => 'Share этот link с водитель. Это works один раз, для этот остановка только, и expires после :hours часы. Issuing новое link cancels любой earlier one.',
            'copy'         => 'Копировать link',
            'no-stop'      => 'Этот отправка есть нет доставка остановка всё ещё открыт, поэтому nothing до capture.',
            'notification' => 'Link issued.',
        ],
    ],
];
