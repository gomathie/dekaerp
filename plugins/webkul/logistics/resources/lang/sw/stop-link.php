<?php

return [
    'title'                  => 'Thibitisha usafirishaji',
    'shipment'               => 'Shehena',
    'stop'                   => 'Kituo',
    'confidential-shipment'  => 'Confidential shehena — cargo maelezo si disclosed.',

    'ttl' => [
        '4'  => '4 saa',
        '8'  => '8 saa',
        '16' => '16 saa',
        '24' => '24 saa (1 siku)',
        '48' => '48 saa (2 siku)',
    ],

    'form' => [
        'recipient-name'      => 'Imepokelewa na',
        'recipient-name-help' => 'jina ya person nani took usafirishaji.',
        'recipient-id'        => 'Mpokeaji Kitambulisho',
        'recipient-id-help'   => 'Kitambulisho au kumbukumbu kutoka person receiving, kama yako kampuni requires.',
        'photo'               => 'Photo',
        'photo-help'          => 'photo ya delivered goods, door, au signed hati ya usafirishaji.',
        'signature'           => 'Signature',
        'signature-help'      => 'Ask mpokeaji hadi ingia box.',
        'signature-clear'     => 'Futa',
        'notes'               => 'Maelezo',
        'location'            => 'Attach yangu mahali',
        'location-attached'   => 'Mahali attached.',
        'location-failed'     => 'Mahali haipatikani. Wewe unaweza bado wasilisha.',
        'submit'              => 'Thibitisha usafirishaji',
        'submitting'          => 'Confirming…',
    ],

    'done' => [
        'title'   => 'Usafirishaji imethibitishwa',
        'message' => 'Thank wewe. usafirishaji ina recorded na hii link ni sasa imefungwa.',
    ],

    'expired' => [
        'title'   => 'Hii link ni hakuna longer halali',
        'message' => 'Hii mei ina expired, tayari inatumika, au withdrawn. Ask office kwa mpya link.',
    ],

    'actions' => [
        'revoke-pod-link' => [
            'label'        => 'Ghairi POD link',
            'heading'      => 'Ghairi proof ya usafirishaji link?',
            'description'  => 'Yoyote link tayari imetumwa kwa hii shehena vituo working immediately. Tumia hii ikiwa link went hadi wrong nambari.',
            'notification' => '{0}ilikuwa hakuna live link hadi ghairi.|{1}Link imeghairiwa.|[2,*]:count links imeghairiwa.',
        ],
        'send-pod-link' => [
            'label'        => 'POD link',
            'heading'      => 'One-time proof ya usafirishaji link',
            'description'  => 'Share hii link na dereva. Hii works mara moja, kwa hii kituo tu, na expires baada ya :hours saa. Issuing mpya link cancels yoyote earlier one.',
            'copy'         => 'Nakili link',
            'no-stop'      => 'Hii shehena ina hakuna usafirishaji kituo bado wazi, hivyo ni nothing hadi capture.',
            'notification' => 'Link issued.',
        ],
    ],
];
