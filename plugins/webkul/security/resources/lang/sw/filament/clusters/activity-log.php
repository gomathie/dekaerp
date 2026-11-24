<?php

return [
    'title'           => 'Kumbukumbu ya shughuli',
    'navigation'      => 'Kumbukumbu ya shughuli',
    'group'           => 'Jumla',
    'system'          => 'Mfumo',
    'export'          => 'Hamisha',
    'export-complete' => 'Faili ya kumbukumbu ya shughuli iko tayari: safu :count.',

    'columns' => [
        'when'    => 'Lini',
        'who'     => 'Nani',
        'record'  => 'Rekodi',
        'action'  => 'Kitendo',
        'changes' => 'Mabadiliko',
    ],

    'filters' => [
        'record-type' => 'Aina ya rekodi',
        'from'        => 'Kuanzia',
        'until'       => 'Hadi',
    ],

    'events' => [
        'created'      => 'Imeundwa',
        'updated'      => 'Imebadilishwa',
        'deleted'      => 'Imefutwa',
        'soft_deleted' => 'Imewekwa kumbukumbu',
        'hard_deleted' => 'Imefutwa',
        'restored'     => 'Imerejeshwa',
        'reset'        => 'Imerudishwa kuwa rasimu',
        'cancelled'    => 'Imeghairiwa',
    ],
];
