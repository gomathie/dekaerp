<?php

return [
    'heading' => 'Chatter',

    'placeholders' => [
        'no-record-found' => 'Hakuna rekodi imepatikana.',
        'loading'         => 'Inapakia Chatter...',
    ],

    'activity-infolist' => [
        'title' => 'Shughuli',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Ghairi Shughuli',
    ],

    'delete-message-action' => [
        'title' => 'Futa Ujumbe',
    ],

    'edit-activity' => [
        'title' => 'Hariri Shughuli',

        'form' => [
            'fields' => [
                'activity-plan' => 'Shughuli Plan',
                'plan-date'     => 'Plan Tarehe',
                'plan-summary'  => 'Plan Muhtasari',
                'activity-type' => 'Shughuli Aina',
                'due-date'      => 'Tarehe ya malipo',
                'summary'       => 'Muhtasari',
                'assigned-to'   => 'Amekabidhiwa Hadi',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Shughuli imesasishwa',
                    'body'  => 'shughuli ina imesasishwa successfully.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Original Maelezo</span>: :body</div>',
        'original-note' => '<br><div><span class="font-bold">Original Maelezo</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Feedback</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title'   => 'Mark kama imekamilika',
        'actions' => [
            'done' => [
                'label' => 'Imekamilika',
            ],
        ],
        'form'  => [
            'fields' => [
                'feedback' => 'Feedback',
            ],
        ],

        'footer-actions' => [
            'label' => 'Imekamilika & Ratiba Ifuatayo',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Shughuli mark kama imekamilika',
                        'body'  => 'shughuli mark kama imekamilika successfully.',
                    ],
                ],
            ],
        ],
    ],
];
