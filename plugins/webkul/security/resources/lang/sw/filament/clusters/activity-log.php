<?php

return [
    'title'           => 'Shughuli log',
    'navigation'      => 'Shughuli log',
    'group'           => 'Jumla',
    'system'          => 'Mfumo',
    'export'          => 'Hamisha',
    'export-complete' => 'Yako shughuli log hamisha ni ready: :count mistari.',

    'columns' => [
        'when'    => 'Wakati',
        'who'     => 'Nani',
        'record'  => 'Rekodi',
        'action'  => 'Kitendo',
        'changes' => 'Changes',
    ],

    'filters' => [
        'record-type' => 'Rekodi aina',
        'from'        => 'Kutoka',
        'until'       => 'Mpaka',
    ],

    'events' => [
        'created'      => 'Imeundwa',
        'updated'      => 'Imebadilishwa',
        'deleted'      => 'Imefutwa',
        'soft_deleted' => 'Imehifadhiwa',
        'hard_deleted' => 'Imefutwa',
        'restored'     => 'Restored',
        'reset'        => 'Weka upya hadi rasimu',
        'cancelled'    => 'Imeghairiwa',
    ],
];
