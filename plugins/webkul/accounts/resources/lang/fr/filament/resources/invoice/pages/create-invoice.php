<?php

return [
    'notification' => [
        'title' => 'Facture créée',
        'body'  => 'La facture a été créée avec succès.',
    ],

    'direct-sale' => [
        'button'           => 'Nouvelle vente directe',
        'walk-in-customer' => 'Client de passage',
    ],

    'receipt' => [
        'label'     => 'Imprimer le reçu',
        'heading'   => 'Reçu',
        'title'     => 'Reçu',
        'number'    => 'Reçu n°',
        'date'      => 'Date',
        'customer'  => 'Client',
        'served-by' => 'Servi par',
        'item'      => 'Article',
        'qty'       => 'Qté',
        'amount'    => 'Montant',
        'subtotal'  => 'Sous-total',
        'tax'       => 'Taxe',
        'total'     => 'Total',
        'paid'      => 'Payé',
        'balance'   => 'Reste à payer',
        'thank-you' => 'Merci de votre confiance.',
    ],

    'sale-currency' => [
        'heading'       => 'Dans quelle devise cette vente a-t-elle été faite ?',
        'description'   => 'Choisissez la devise dans laquelle le client paie. Le document est émis dans cette devise, et sa valeur dans la devise de la société est affichée à côté des totaux.',
        'base'          => ':code (devise de la société)',
        'continue'      => 'Continuer',
        'rate'          => 'Taux de change',
        'feed'          => 'Taux du jour issu du flux quotidien, daté du :date. Modifiez-le si vous avez convenu d\'un autre taux.',
        'stale'         => 'Le dernier taux enregistré date du :date, il y a plus de 3 jours. Vérifiez-le avant de continuer.',
        'no-rate'       => 'Aucun taux n\'est enregistré pour cette devise. Saisissez le taux convenu.',
        'chatter'       => 'Taux de change confirmé à la création de la facture : :rate.',
        'chatter-order' => 'Taux de change confirmé à la création du devis : :rate.',
        'change'        => 'Devise et taux',
        'kept'          => 'Taux retenu pour cette vente : :rate.',
        'kept-by'       => 'Taux retenu pour cette vente : :rate, confirmé par :name.',
        'dropped'       => 'Le taux confirmé portait sur une autre devise : ce document utilise le taux du jour.',
    ],

    'form' => [
        'actions' => [
            'create-and-confirm' => 'Créer et confirmer',
            'save-draft'         => 'Enregistrer comme brouillon',
        ],
    ],
];
