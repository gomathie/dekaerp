<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'Weka imechukuliwa',
            'notification'         => 'Usafirishaji umewekwa kuwa umechukuliwa',
            'transit-label'        => 'Anza usafiri',
            'transit-notification' => 'Usafirishaji umewekwa kuwa njiani',
        ],
        'deliver' => [
            'label'                         => 'Wasilisha usafirishaji',
            'notification'                  => 'Usafirishaji umewasilishwa',
            'stop-notification'             => 'Kituo cha uwasilishaji kimerekodiwa',
            'out-for-delivery-label'        => 'Weka kwenye uwasilishaji',
            'out-for-delivery-notification' => 'Usafirishaji umewekwa kwenye uwasilishaji',
            'fields'                        => [
                'capture-pod'    => 'Rekodi uthibitisho wa uwasilishaji',
                'stop'           => 'Kituo cha uwasilishaji',
                'recipient-name' => 'Jina la mpokeaji',
                'received-at'    => 'Muda wa kupokea',
                'reference'      => 'Marejeleo',
                'notes'          => 'Vidokezo',
                'photo'          => 'Picha ya uwasilishaji',
                'signature'      => 'Sahihi ya mpokeaji',
            ],
        ],
        'fail' => [
            'label'               => 'Rekodi uwasilishaji ulioshindwa',
            'reason'              => 'Sababu',
            'notification'        => 'Uwasilishaji ulioshindwa umerekodiwa',
            'resolve-label'       => 'Shughulikia uwasilishaji ulioshindwa',
            'resolution'          => 'Hatua inayofuata',
            'retry-notification'  => 'Usafirishaji uko tayari kwa jaribio jingine la uwasilishaji',
            'return-notification' => 'Usafirishaji umewekwa kuwa umerejeshwa',
            'resolutions'         => [
                'retry'  => 'Jaribu tena uwasilishaji',
                'return' => 'Weka umerejeshwa',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'Uthibitisho wa uwasilishaji',
        'columns' => [
            'recipient'    => 'Mpokeaji',
            'received-at'  => 'Muda wa kupokea',
            'stop'         => 'Kituo',
            'captured-via' => 'Imerekodiwa kupitia',
            'photo'        => 'Picha',
            'signature'    => 'Sahihi',
        ],
    ],
    'validation' => [
        'pod-required'        => 'Uthibitisho wa uwasilishaji unahitajika kabla usafirishaji huu haujawasilishwa.',
        'stop-required'       => 'Chagua kituo cha uwasilishaji ambacho uthibitisho huu ni wake.',
        'stop-unavailable'    => 'Kituo cha uwasilishaji kilichochaguliwa hakipatikani tena.',
        'upload-failed'       => 'Faili ya uthibitisho haikuweza kuhifadhiwa. Tafadhali jaribu tena.',
        'resolution-required' => 'Chagua kujaribu tena au kurejesha uwasilishaji ulioshindwa.',
    ],
];
