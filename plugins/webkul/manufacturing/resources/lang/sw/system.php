<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => "Baadhi agizo la kazis ni tayari imekamilika, hivyo wewe haiwezekani un-plan hii uzalishaji agizo.nnIt'd kuwa shame hadi waste zote hiyo maendeleo, kulia?",
            'work-orders-already-started' => "Baadhi agizo la kazis ina tayari started, hivyo wewe haiwezekani un-plan hii uzalishaji agizo.nnIt'd kuwa shame hadi waste zote hiyo maendeleo, kulia?",
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Time Tracking: :name',
        'no-performance-productivity-loss' => "Wewe need hadi define saa kidogo one un archive productivity hasara katika kategoria 'Performance'. Unda kutoka usanidi mipangilio.",
    ],

    'work-center' => [
        'already-unblocked' => 'Hii ina tayari unblocked.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Tafadhali unblock work center hadi anza agizo la kazi.',
        'already-done-or-cancelled'  => 'Wewe haiwezekani anza agizo la kazi hiyo ni tayari imekamilika au imeghairiwa',
        'no-calendar-on-work-center' => 'ni hakuna defined kalenda washa work center :name.',
        'no-productivity-loss'       => "Wewe need hadi define saa kidogo one productivity hasara katika kategoria 'Productivity'. Unda Usanidi mipangilio.",
        'no-performance-loss'        => "Wewe need hadi define saa kidogo one productivity hasara katika kategoria 'Performance'. Unda Usanidi mipangilio.",
        'impossible-to-plan'         => 'Impossible hadi plan agizo la kazi. Tafadhali hundi work center availabilities.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'Wewe haiwezekani ina :product kama finished bidhaa na katika Byproducts',
        'missing-lot-serial-number'                => 'Wewe need hadi supply Kundi/Nambari ya mfululizo Nambari kwa bidhaa na "consume" wao: :missing_products',
        'serial-number-already-produced'           => 'Hii nambari ya mfululizo nambari kwa bidhaa :product ina tayari produced',
        'byproduct-serial-number-already-produced' => 'nambari ya mfululizo nambari :number inatumika kwa byproduct :product ina tayari produced',
        'component-serial-number-consumed'         => 'nambari ya mfululizo nambari :number inatumika kwa component :component ina tayari consumed',
        'components-availability'                  => [
            'available'     => 'Inapatikana',
            'not-available' => 'Si Inapatikana',
            'expected'      => 'Inatarajiwa :date',
        ],
    ],
];
