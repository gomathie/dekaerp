<?php

return [
    'navigation' => [
        'title' => 'Catégories de dépense',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'Justificatif requis',
            'is-subcontracting'        => 'Coût de sous-traitant ou de transporteur',
            'is-subcontracting-helper' => 'Les coûts de cette catégorie sont payés à un fournisseur (transporteur, agent) et facturés par fournisseur.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'Justificatif requis',
            'is-subcontracting' => 'Sous-traitance',
        ],
    ],
];
