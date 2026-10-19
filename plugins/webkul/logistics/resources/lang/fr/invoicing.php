<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Temps d’attente à :stop (:minutes min au-delà du temps gratuit)',
    ],

    'charges' => [
        'title'  => 'Frais',
        'fields' => [
            'product'            => 'Service',
            'description'        => 'Description',
            'quantity'           => 'Quantité',
            'price-unit'         => 'Prix unitaire',
            'discount'           => 'Remise (%)',
            'taxes'              => 'Taxes',
            'is-billable'        => 'Facturable',
            'is-billable-helper' => 'Seuls les frais facturables sont repris lors de la création d’une facture.',
            'invoiced'           => 'Facturé',
        ],
    ],

    'invoices' => [
        'title'   => 'Factures',
        'columns' => [
            'number'        => 'Facture',
            'state'         => 'Statut',
            'payment-state' => 'Paiement',
            'date'          => 'Date',
            'total'         => 'Total',
        ],
        'actions' => [
            'open' => 'Ouvrir dans Comptabilité',
        ],
    ],

    'unbilled' => [
        'title'   => 'Frais non facturés',
        'columns' => [
            'shipment'    => 'Expédition',
            'customer'    => 'Client',
            'description' => 'Frais',
            'quantity'    => 'Qté',
            'subtotal'    => 'Montant',
            'total'       => 'Total',
        ],
        'filters' => [
            'currency' => 'Devise',
            'shipment' => 'Expédition',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'Créer une facture',
            'heading'            => 'Facturer cette expédition ?',
            'description'        => 'Chaque frais facturable qui n’a pas encore été facturé devient une ligne d’une nouvelle facture client. Les frais déjà facturés restent inchangés.',
            'notification'       => 'Facture créée.',
            'notification-body'  => 'La facture :invoice est dans Comptabilité, sous forme de brouillon que vous pouvez vérifier avant de la comptabiliser.',
            'nothing-to-invoice' => 'Rien à facturer',
        ],
    ],
];
