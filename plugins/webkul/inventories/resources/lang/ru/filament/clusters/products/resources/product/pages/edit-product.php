<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Ошибка updating tracking',
                    'body'  => 'Вы можно не изменить склад tracking товар тот был уже используется.',
                ],

                'reordering-rules' => [
                    'title' => 'Ошибка updating товар',
                    'body'  => 'Вы всё ещё есть некоторые активен reordering rules вкл. этот товар. Пожалуйста archive или удалить их первый.',
                ],

                'reserved' => [
                    'title' => 'Ошибка updating tracking',
                    'body'  => 'Вы можно не изменить склад tracking товар тот currently зарезервировано вкл. запас переместить. Если вы need до изменить склад tracking, вы следует первый unreserve запас переместить.',
                ],

                'qty-not-zero' => [
                    'title' => 'Ошибка updating tracking',
                    'body'  => 'Доступно количество следует быть задать до zero до changing склад tracking.',
                ],

                'track-by-update' => [
                    'title' => 'Ошибка updating tracking',
                    'body'  => 'Вы есть товар(s) в запас тот есть нет партия/серийный номер номер. Вы можно назначить партия/серийный номер numbers от doing склад adjustment.',
                ],
            ],
        ],
    ],
];
