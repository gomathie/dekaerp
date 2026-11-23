<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Nothing hadi hundi availability kwa.',
        ],

        'cancel-move' => [
            'already-done' => 'Wewe haiwezekani ghairi hisa hamisha hiyo ina weka hadi \'Done\'. Unda return katika agizo hadi reverse moves ambayo took place.',
        ],

        'unreserve-move' => [
            'already-done' => "Wewe unaweza si unreserve hisa hamisha hiyo ina weka hadi 'Done'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'kiasi imekamilika kwa bidhaa ":product" doesn\'t respect rounding precision defined washa kipimo ":unit". Tafadhali badilisha kiasi imekamilika au rounding precision ya yako uniti ya measure.',
            'no-negative-quantities'     => 'Hakuna negative quantities inaruhusiwa',
            'missing-lot-serial-number'  => "Wewe need hadi supply Kundi/Nambari ya mfululizo Nambari kwa bidhaa:n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "Hakuna rule ina imepatikana hadi replenish \":product\" katika \":location\".nVerify njia usanidi washa bidhaa.",
            'no-source-location' => 'Hakuna chanzo mahali defined washa hisa rule: :name!',
            'no-vendor-price'    => 'ni hakuna matching msambazaji bei hadi generate agizo la ununuzi kwa bidhaa :product (hakuna msambazaji defined, minimum kiasi si reached, dates si halali,...). Go washa bidhaa fomu na kamilisha orodha ya wasambazaji.',
        ],

        'return' => [
            'origin' => 'Return ya :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'Reserving negative kiasi ni si inaruhusiwa.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'Kiasi au Imehifadhiwa Kiasi unapaswa kuwa weka.',
        'removal-strategy-not-implemented' => 'Removal strategy :strategy si implemented.',
        'unreserve-more-than-stock'        => 'Hii ni si possible hadi unreserve zaidi bidhaa ya :name kuliko wewe ina katika hisa.',
    ],

    'product' => [
        'endless-loop-rule' => "Si halali rule's usanidi, ifuatayo rule causes endless loop: :name",
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'kiasi imekamilika kwa bidhaa :product doesn\'t respect rounding precision defined washa kipimo :unit. Tafadhali badilisha kiasi imekamilika au rounding precision ya yako uniti ya measure.',
        'split-done-or-cancel'       => 'Wewe haiwezekani split hisa hamisha hiyo ina weka hadi \'Done\' au \'Cancel\'.',
        'split-draft'                => 'Wewe haiwezekani split rasimu hamisha. Hii needs hadi kuwa imethibitishwa kwanza.',
        'serial-already-assigned'    => 'nambari ya mfululizo nambari ina tayari amekabidhiwa hadi Bidhaa: :product, Nambari ya mfululizo Nambari: :serial_number',

        'cross-company' => [
            'title' => 'Cross-kampuni uhamisho si inaruhusiwa',
            'body'  => 'uhamisho haiwezekani hamisha hisa directly kati ya maeneo hiyo belong hadi different makampuni (:source na :destination). Inter-kampuni uhamisho ni si supported bado.',
        ],
    ],

    'rule' => [
        'delay-on'     => 'Delay washa :name',
        'days'         => '+ :days siku(s)',
        'time-horizon' => 'Time Horizon',
    ],
];
