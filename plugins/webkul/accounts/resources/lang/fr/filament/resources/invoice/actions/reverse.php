<?php

return [
    'title' => 'Extourner',

    'modal' => [
        'heading' => 'Créer une extourne',

        'form' => [
            'reason'  => 'Motif affiché sur l\'extourne',
            'journal' => 'Journal',
            'date'    => 'Date',
        ],
    ],

    'one-step' => [
        'scope'               => 'Ce qu\'il faut créditer',
        'whole'               => 'Toute la facture',
        'lines'               => 'Certaines lignes',
        'line-list'           => 'Lignes',
        'disposition'         => 'Ensuite',
        'apply'               => 'La déduire de la facture',
        'refund'              => 'La rembourser',
        'credit'              => 'La garder comme avoir',
        'saved'               => 'Avoir comptabilisé.',
        'history'             => 'Avoir comptabilisé (:disposition). Motif : :reason',
        'reason'              => 'Un motif est obligatoire.',
        'invoice'             => 'Seule une facture client comptabilisée peut être créditée ainsi.',
        'lines-invalid'       => 'Choisissez des lignes de cette facture.',
        'disposition-invalid' => 'Choisissez quoi faire de l\'avoir.',
        'not-allowed'         => 'Vous n\'êtes pas autorisé à rembourser.',
        'company'             => 'Vous ne pouvez pas créditer une facture de cette société.',
        'journal'             => 'Choisissez un journal de banque, de caisse ou de carte de cette société.',
        'not-settled'         => 'L\'argent n\'a pas été remboursé.',
        'not-applied'         => 'L\'avoir n\'a pas été déduit de la facture.',
    ],
];
