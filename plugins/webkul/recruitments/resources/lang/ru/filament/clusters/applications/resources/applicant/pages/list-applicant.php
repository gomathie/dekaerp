<?php

return [
    'notification' => [
        'title' => 'Кандидат создано',
        'body'  => 'кандидат есть создано successf ully.',
    ],

    'tabs' => [
        'my-applicants'          => 'Мой Кандидаты',
        'un-assigned'            => 'Un Назначен',
        'in-progress'            => 'В Ход',
        'hired'                  => 'Hired',
        'refused'                => 'Отказано',
        'archived'               => 'В архиве',
        'blocked'                => 'Blocked',
        'directly-available'     => 'Directly Доступно',
        'created-recently'       => 'Создано Recently',
        'stage-updated-recently' => 'Этап Обновлено Recently',
    ],

    'header-actions' => [
        'create-applicant' => [
            'label' => 'Новое Кандидат',

            'modal-title' => 'Создать Кандидат',

            'form' => [
                'fields' => [
                    'candidate' => 'Candidate',
                ],
            ],
        ],
    ],
];
