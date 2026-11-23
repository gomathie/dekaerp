<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'Mark picked juu',
            'notification'         => 'Shehena marked kama picked juu',
            'transit-label'        => 'Anza transit',
            'transit-notification' => 'Shehena marked katika transit',
        ],
        'deliver' => [
            'label'                         => 'Deliver shehena',
            'notification'                  => 'Shehena delivered',
            'stop-notification'             => 'Usafirishaji kituo recorded',
            'out-for-delivery-label'        => 'Mark nje kwa usafirishaji',
            'out-for-delivery-notification' => 'Shehena marked nje kwa usafirishaji',
            'fields'                        => [
                'capture-pod'    => 'Capture proof ya usafirishaji',
                'stop'           => 'Usafirishaji kituo',
                'recipient-name' => 'Mpokeaji jina',
                'received-at'    => 'Imepokelewa saa',
                'reference'      => 'Kumbukumbu',
                'notes'          => 'Maelezo',
                'photo'          => 'Usafirishaji photo',
                'signature'      => 'Mpokeaji signature',
            ],
        ],
        'fail' => [
            'label'               => 'Rekodi imeshindwa usafirishaji',
            'reason'              => 'Sababu',
            'notification'        => 'Imeshindwa usafirishaji recorded',
            'resolve-label'       => 'Resolve imeshindwa usafirishaji',
            'resolution'          => 'Ifuatayo step',
            'retry-notification'  => 'Shehena ready kwa another usafirishaji attempt',
            'return-notification' => 'Shehena marked kama returned',
            'resolutions'         => [
                'retry'  => 'Jaribu tena usafirishaji',
                'return' => 'Mark returned',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'Proof ya usafirishaji',
        'columns' => [
            'recipient'    => 'Mpokeaji',
            'received-at'  => 'Imepokelewa saa',
            'stop'         => 'Kituo',
            'captured-via' => 'Captured kupitia',
            'photo'        => 'Photo',
            'signature'    => 'Signature',
        ],
    ],
    'validation' => [
        'pod-required'        => 'Proof ya usafirishaji ni inahitajika kabla ya hii shehena unaweza kuwa delivered.',
        'stop-required'       => 'Chagua usafirishaji kituo hii proof belongs hadi.',
        'stop-unavailable'    => 'imechaguliwa usafirishaji kituo ni hakuna longer inapatikana.',
        'upload-failed'       => 'proof faili could si kuwa stored. Tafadhali jaribu tena.',
        'resolution-required' => 'Chagua whether hadi jaribu tena au return imeshindwa usafirishaji.',
    ],
];
