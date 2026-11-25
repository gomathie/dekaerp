<?php

return [
    'statement-name' => 'Taarifa ya :from hadi :to',
    'entry-label'    => 'Mstari wa taarifa ya benki',

    'fields' => [
        'date'    => 'tarehe',
        'amount'  => 'kiasi',
        'balance' => 'salio',
    ],

    'errors' => [
        'not-allowed'            => 'Huwezi kulinganisha akaunti hii ya benki.',
        'not-a-bank-journal'     => 'Chagua jarida la benki au la fedha taslimu.',
        'foreign-currency'       => 'Jarida hili linatumia sarafu ya kigeni. Kwa sasa ni akaunti za benki zilizo kwa sarafu ya kampuni pekee zinazoweza kulinganishwa.',
        'missing-bank-account'   => 'Jarida hili halina akaunti ya benki. Kwanza weka akaunti yake chaguomsingi.',
        'empty-file'             => 'Faili halina mistari ya taarifa.',
        'too-many-rows'          => 'Faili lina zaidi ya mistari :max. Ligawe katika taarifa ndogo zaidi.',
        'bad-row'                => 'Mstari :row: :field haisomeki. Kagua nambari za safu wima na muundo wa tarehe.',
        'already-reconciled'     => 'Mstari huu tayari umelinganishwa.',
        'payment-does-not-match' => 'Malipo hayo hayalingani na mstari huu: lazima yawe kwenye jarida lilelile la benki, kwa kiasi na mwelekeo uleule, na yasiwe yamelinganishwa tayari.',
        'account-not-allowed'    => 'Akaunti hiyo haiwezi kutumika hapa. Akaunti za wateja na wasambazaji hulipwa kwa kulinganisha malipo.',
    ],
];
