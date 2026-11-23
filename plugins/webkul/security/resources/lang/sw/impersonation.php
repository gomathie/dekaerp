<?php

return [
    'banner' => [
        'message' => 'Wewe ni signed katika kama :target (:company). Impersonated na :impersonator.',
        'ends-at' => 'Ends saa :time',
        'end'     => 'Mwisho impersonation',
    ],

    'notifications' => [
        'started' => 'Wewe ni sasa signed katika kama :name.',
        'refused' => 'Impersonation ilikuwa si started',

        'ended-automatically' => [
            'expired' => 'impersonation reached yake time limit na ina ended.',
            'invalid' => 'impersonation ni hakuna longer inaruhusiwa na ina ended.',
        ],

        'granted' => 'Impersonation access granted.',
        'revoked' => 'Impersonation access revoked.',
    ],

    'actions' => [
        'impersonate' => [
            'label'             => 'Impersonate',
            'modal-heading'     => 'Ingia kama :name',
            'modal-description' => 'Wewe ita act kama hii mtumiaji, na yao ruhusa tu, kwa saa wengi :minutes dakika. Everything wewe ni recorded na yako jina.',
            'submit'            => 'Anza impersonation',

            'form' => [
                'reason'   => 'Sababu',
                'password' => 'Yako nenosiri',
            ],
        ],

        'grant' => [
            'label'             => 'Ruhusu impersonation',
            'modal-description' => 'Hii Multi-Kampuni Msimamizi ita kuwa able hadi impersonate tenant watumiaji katika yao amekabidhiwa makampuni.',
        ],

        'revoke' => [
            'label'             => 'Ondoa impersonation access',
            'modal-description' => 'impersonation wao ni running sasa ends washa yao ifuatayo ukurasa load.',
        ],
    ],
];
