<?php

return [
    'validation' => [
        'upload-failed' => 'Le fichier du justificatif n’a pas pu être stocké. Veuillez réessayer.',
    ],
    'relation-manager' => [
        'title' => 'Coûts',

        'fields' => [
            'date'           => 'Date',
            'category'       => 'Catégorie',
            'payee'          => 'Payé à',
            'amount'         => 'Montant',
            'approved-total' => 'Approuvé',
            'state'          => 'Statut',
            'bill'           => 'Facture fournisseur',
            'not-billed'     => 'Non facturé',
        ],
    ],

    'margin' => [
        'heading' => 'Revenus et coûts',
        'revenue' => 'Revenus',
        'costs'   => 'Coûts',
        'margin'  => 'Marge',
        'helper'  => 'Frais facturables comparés aux coûts approuvés. Les coûts en brouillon et rejetés ne sont pas comptabilisés.',
    ],

    'actions' => [
        'post-bill' => [
            'label'          => 'Créer une facture fournisseur',
            'heading'        => 'Créer des factures fournisseurs en brouillon ?',
            'description'    => 'Chaque coût approuvé et non facturé de cette expédition devient une facture en brouillon, une par bénéficiaire. Rien n’est comptabilisé : le service financier doit encore les vérifier et les comptabiliser.',
            'cannot-post'    => 'Ces coûts ne peuvent pas encore être facturés',
            'draft-reminder' => 'Créées comme brouillons. Vérifiez-les et comptabilisez-les dans Comptabilité.',
            'notification'   => '{0}Il ne restait rien à facturer.|{1}Une facture fournisseur en brouillon a été créée.|[2,*]:count factures fournisseurs en brouillon ont été créées.',
        ],
    ],
];
