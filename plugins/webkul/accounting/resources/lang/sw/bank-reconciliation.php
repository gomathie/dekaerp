<?php

return [
    'statement-name' => 'Taarifa :from hadi :to',
    'entry-label'    => 'Benki taarifa mstari',

    'fields' => [
        'date'   => 'tarehe',
        'amount' => 'kiasi',
    ],

    'errors' => [
        'not-allowed'            => 'Wewe haiwezekani patanisha hii akaunti ya benki.',
        'not-a-bank-journal'     => 'Chagua benki au taslimu jarida.',
        'foreign-currency'       => 'Hii jarida uses foreign sarafu. Tu akaunti ya benkis katika kampuni sarafu unaweza kuwa reconciled kwa sasa.',
        'missing-bank-account'   => 'Hii jarida ina hakuna akaunti ya benki weka. Weka yake chaguo-msingi akaunti kwanza.',
        'empty-file'             => 'faili ina hakuna taarifa mistari.',
        'too-many-rows'          => 'faili ina zaidi kuliko :max mistari. Split hii ndani smaller statements.',
        'bad-row'                => 'Mstari :row: :field haiwezekani kuwa kusoma. Hundi safu numbers na tarehe umbizo.',
        'already-reconciled'     => 'Hii mstari ni tayari reconciled.',
        'payment-does-not-match' => 'Hiyo malipo si kilinganacho hii mstari: hii lazima kuwa katika ile ile benki jarida, kwa ile ile kiasi na direction, na si tayari matched.',
        'account-not-allowed'    => 'Hiyo akaunti haiwezekani kuwa inatumika hapa. Mteja na msambazaji akaunti ni settled na matching malipo.',
    ],
];
