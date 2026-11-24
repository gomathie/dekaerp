<?php

return [
    'notification' => [
        'title' => 'Соискатель создан',
        'body'  => 'Соискатель успешно создан.',
    ],

    'tabs' => [
        'my-applicants'          => 'Мои соискатели',
        'un-assigned'            => 'Без ответственного',
        'in-progress'            => 'В работе',
        'hired'                  => 'Нанят',
        'refused'                => 'Отказано',
        'archived'               => 'В архиве',
        'blocked'                => 'Заблокирован',
        'directly-available'     => 'Готов приступить',
        'created-recently'       => 'Недавно созданные',
        'stage-updated-recently' => 'Недавно сменили этап',
    ],

    'header-actions' => [
        'create-applicant' => [
            'label' => 'Новый соискатель',

            'modal-title' => 'Создать соискателя',

            'form' => [
                'fields' => [
                    'candidate' => 'Кандидат',
                ],
            ],
        ],
    ],
];
