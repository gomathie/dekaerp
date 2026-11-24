<?php

return [
    'banner' => [
        'message' => 'Umeingia kama :target (:company). Uigaji unafanywa na :impersonator.',
        'ends-at' => 'Inaisha saa :time',
        'end'     => 'Maliza uigaji',
    ],

    'notifications' => [
        'started' => 'Sasa umeingia kama :name.',
        'refused' => 'Uigaji haukuanzishwa',

        'ended-automatically' => [
            'expired' => 'Uigaji umefikia kikomo cha muda na umeisha.',
            'invalid' => 'Uigaji hauruhusiwi tena na umeisha.',
        ],

        'granted' => 'Ruhusa ya uigaji imetolewa.',
        'revoked' => 'Ruhusa ya uigaji imebatilishwa.',
    ],

    'actions' => [
        'impersonate' => [
            'label'             => 'Iga mtumiaji',
            'modal-heading'     => 'Ingia kama :name',
            'modal-description' => 'Utatenda kama mtumiaji huyu, kwa ruhusa zake pekee, kwa muda usiozidi dakika :minutes. Kila unachofanya kinarekodiwa kwa jina lako.',
            'submit'            => 'Anza uigaji',

            'form' => [
                'reason'   => 'Sababu',
                'password' => 'Nenosiri lako',
            ],
        ],

        'grant' => [
            'label'             => 'Ruhusu uigaji',
            'modal-description' => 'Multi-Company Admin huyu ataweza kuiga watumiaji wa wateja katika kampuni alizogawiwa.',
        ],

        'revoke' => [
            'label'             => 'Ondoa ruhusa ya uigaji',
            'modal-description' => 'Uigaji anaoendesha sasa utaisha atakapopakia ukurasa unaofuata.',
        ],
    ],
];
