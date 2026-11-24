<?php

return [
    'heading' => 'Mazungumzo',

    'placeholders' => [
        'no-record-found' => 'Hakuna rekodi iliyopatikana.',
        'loading'         => 'Inapakia mazungumzo...',
    ],

    'activity-infolist' => [
        'title' => 'Shughuli',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Ghairi shughuli',
    ],

    'delete-message-action' => [
        'title' => 'Futa ujumbe',
    ],

    'edit-activity' => [
        'title' => 'Hariri shughuli',

        'form' => [
            'fields' => [
                'activity-plan' => 'Mpango wa shughuli',
                'plan-date'     => 'Tarehe ya mpango',
                'plan-summary'  => 'Muhtasari wa mpango',
                'activity-type' => 'Aina ya shughuli',
                'due-date'      => 'Tarehe ya mwisho',
                'summary'       => 'Muhtasari',
                'assigned-to'   => 'Amekabidhiwa',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Shughuli imesasishwa',
                    'body'  => 'Shughuli imesasishwa kikamilifu.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Kidokezo cha awali</span>: :body</div>',
        'original-note' => '<br><div><span class="font-bold">Kidokezo cha awali</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Maoni</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title'   => 'Weka kuwa imekamilika',
        'actions' => [
            'done' => [
                'label' => 'Imekamilika',
            ],
        ],
        'form'  => [
            'fields' => [
                'feedback' => 'Maoni',
            ],
        ],

        'footer-actions' => [
            'label' => 'Kamilisha na panga inayofuata',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Shughuli imekamilika',
                        'body'  => 'Shughuli imewekwa kuwa imekamilika.',
                    ],
                ],
            ],
        ],
    ],
];
