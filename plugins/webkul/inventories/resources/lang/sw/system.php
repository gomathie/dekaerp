<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Hakuna cha kuangalia upatikanaji wake.',
        ],

        'cancel-move' => [
            'already-done' => 'Huwezi kughairi mwendo wa bidhaa uliowekwa «Imekamilika». Unda marejesho ili kubatilisha mienendo iliyofanyika.',
        ],

        'unreserve-move' => [
            'already-done' => 'Huwezi kuondoa utengaji kwenye mwendo wa bidhaa uliowekwa «Imekamilika».',
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'Idadi iliyokamilika ya bidhaa «:product» haizingatii usahihi wa ukadiriaji wa kipimo «:unit». Tafadhali badilisha idadi iliyokamilika au usahihi wa ukadiriaji wa kipimo chako.',
            'no-negative-quantities'     => 'Idadi hasi haziruhusiwi',
            'missing-lot-serial-number'  => "Unahitaji kuweka kundi/nambari ya mfululizo kwa bidhaa:\n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "Hakuna kanuni iliyopatikana ya kujaza upya «:product» katika «:location».\nHakiki mipangilio ya njia kwenye bidhaa.",
            'no-source-location' => 'Hakuna eneo la chanzo lililowekwa kwenye kanuni ya bidhaa: :name!',
            'no-vendor-price'    => 'Hakuna bei ya msambazaji inayolingana ili kuunda oda ya ununuzi kwa bidhaa :product (hakuna msambazaji, idadi ya chini haijafikiwa, tarehe si halali, ...). Fungua fomu ya bidhaa na ukamilishe orodha ya wasambazaji.',
        ],

        'return' => [
            'origin' => 'Marejesho ya :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'Kutenga idadi hasi hakuruhusiwi.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'Idadi au idadi iliyotengwa inapaswa kuwekwa.',
        'removal-strategy-not-implemented' => 'Mkakati wa kutoa :strategy haujatekelezwa.',
        'unreserve-more-than-stock'        => 'Haiwezekani kuondoa utengaji wa bidhaa nyingi za :name kuliko ulizonazo ghalani.',
    ],

    'product' => [
        'endless-loop-rule' => 'Mipangilio ya kanuni si sahihi, kanuni ifuatayo husababisha mzunguko usio na mwisho: :name',
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'Idadi iliyokamilika ya bidhaa :product haizingatii usahihi wa ukadiriaji wa kipimo :unit. Tafadhali badilisha idadi iliyokamilika au usahihi wa ukadiriaji wa kipimo chako.',
        'split-done-or-cancel'       => 'Huwezi kugawa mwendo wa bidhaa uliowekwa «Imekamilika» au «Imeghairiwa».',
        'split-draft'                => 'Huwezi kugawa mwendo wa rasimu. Unahitaji kuthibitishwa kwanza.',
        'serial-already-assigned'    => 'Nambari ya mfululizo tayari imewekwa kwa bidhaa: :product, nambari ya mfululizo: :serial_number',

        'cross-company' => [
            'title' => 'Uhamisho kati ya kampuni hauruhusiwi',
            'body'  => 'Uhamisho hauwezi kuhamisha bidhaa moja kwa moja kati ya maeneo ya kampuni tofauti (:source na :destination). Uhamisho kati ya kampuni bado hautumiki.',
        ],
    ],

    'rule' => [
        'delay-on'     => 'Ucheleweshaji wa :name',
        'days'         => '+ siku :days',
        'time-horizon' => 'Kipindi cha mpango',
    ],
];
