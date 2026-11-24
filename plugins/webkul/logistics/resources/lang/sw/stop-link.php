<?php

return [
    'title'                  => 'Thibitisha uwasilishaji',
    'shipment'               => 'Usafirishaji',
    'stop'                   => 'Kituo',
    'confidential-shipment'  => 'Usafirishaji wa siri — maelezo ya mzigo hayajawekwa wazi.',

    'ttl' => [
        '4'  => 'Saa 4',
        '8'  => 'Saa 8',
        '16' => 'Saa 16',
        '24' => 'Saa 24 (siku 1)',
        '48' => 'Saa 48 (siku 2)',
    ],

    'form' => [
        'recipient-name'      => 'Imepokelewa na',
        'recipient-name-help' => 'Jina la mtu aliyepokea mzigo.',
        'recipient-id'        => 'Kitambulisho cha mpokeaji',
        'recipient-id-help'   => 'Kitambulisho au marejeleo kutoka kwa mpokeaji, kama kampuni yako inavyohitaji.',
        'photo'               => 'Picha',
        'photo-help'          => 'Picha ya bidhaa zilizowasilishwa, mlango, au hati ya usafirishaji iliyosainiwa.',
        'signature'           => 'Sahihi',
        'signature-help'      => 'Mwombe mpokeaji asaini kwenye kisanduku.',
        'signature-clear'     => 'Futa',
        'notes'               => 'Vidokezo',
        'location'            => 'Ambatisha mahali nilipo',
        'location-attached'   => 'Mahali pameambatishwa.',
        'location-failed'     => 'Mahali hapapatikani. Bado unaweza kuwasilisha.',
        'submit'              => 'Thibitisha uwasilishaji',
        'submitting'          => 'Inathibitisha…',
    ],

    'done' => [
        'title'   => 'Uwasilishaji umethibitishwa',
        'message' => 'Asante. Uwasilishaji umerekodiwa na kiungo hiki sasa kimefungwa.',
    ],

    'expired' => [
        'title'   => 'Kiungo hiki si halali tena',
        'message' => 'Huenda muda wake umeisha, kimeshatumika, au kimeondolewa. Omba ofisi kiungo kipya.',
    ],

    'actions' => [
        'revoke-pod-link' => [
            'label'        => 'Ghairi kiungo cha uthibitisho',
            'heading'      => 'Ghairi kiungo cha uthibitisho wa uwasilishaji?',
            'description'  => 'Kiungo chochote kilichokwisha tumwa kwa usafirishaji huu kinaacha kufanya kazi mara moja. Tumia hii ikiwa kiungo kilitumwa kwa namba isiyo sahihi.',
            'notification' => '{0}Hakukuwa na kiungo hai cha kughairi.|{1}Kiungo kimeghairiwa.|[2,*]Viungo :count vimeghairiwa.',
        ],
        'send-pod-link' => [
            'label'        => 'Kiungo cha uthibitisho',
            'heading'      => 'Kiungo cha mara moja cha uthibitisho wa uwasilishaji',
            'description'  => 'Shiriki kiungo hiki na dereva. Kinafanya kazi mara moja, kwa kituo hiki tu, na kinaisha baada ya saa :hours. Kutoa kiungo kipya kunaghairi kiungo chochote cha awali.',
            'copy'         => 'Nakili kiungo',
            'no-stop'      => 'Usafirishaji huu hauna kituo cha uwasilishaji kilicho wazi, hivyo hakuna cha kurekodi.',
            'notification' => 'Kiungo kimetolewa.',
        ],
    ],
];
