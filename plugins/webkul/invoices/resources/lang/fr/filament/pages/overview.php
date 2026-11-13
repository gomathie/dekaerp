<?php

return [
    'navigation' => [
        'title' => 'Vue d’ensemble',
    ],

    'title' => 'Vue d’ensemble',

    'sections' => [
        'sales'     => 'Ventes',
        'purchases' => 'Achats',
        'invoices'  => 'Factures et factures fournisseurs',
    ],

    'filters' => [
        'sections'       => 'Afficher',
        'all-sections'   => 'Toutes les sections',
        'date-range'     => 'Période',
        'product'        => 'Produit',
        'all-products'   => 'Tous les produits',
        'category'       => 'Catégorie',
        'all-categories' => 'Toutes les catégories',
    ],

    'headline' => [
        'heading'         => 'En un coup d’œil',
        'sales'           => 'Ventes',
        'purchases'       => 'Achats',
        'invoices'        => 'Factures',
        'revenue'         => 'Chiffre d’affaires',
        'orders'          => '{0} Aucune commande|{1} 1 commande|[2,*] :count commandes',
        'open-quotations' => 'Devis ouverts',
        'spend'           => 'Dépenses d’achat',
        'open-rfqs'       => 'Demandes de prix ouvertes',
        'to-collect'      => 'À encaisser',
        'to-pay'          => 'À payer',
        'overdue'         => '{0} Aucun en retard|{1} 1 en retard|[2,*] :count en retard',
    ],

    'attention' => [
        'heading'          => 'À traiter',
        'empty'            => 'Tout est en ordre. Rien ne vous attend.',
        'overdue-invoices' => '{1} 1 facture en retard|[2,*] :count factures en retard',
        'overdue-bills'    => '{1} 1 facture fournisseur en retard|[2,*] :count factures fournisseur en retard',
        'bills-due-soon'   => '{1} 1 facture fournisseur à payer cette semaine|[2,*] :count factures fournisseur à payer cette semaine',
        'old-quotations'   => '{1} 1 devis en attente depuis plus de 7 jours|[2,*] :count devis en attente depuis plus de 7 jours',
        'rfqs-to-approve'  => '{1} 1 achat à approuver|[2,*] :count achats à approuver',
    ],

    'actions' => [
        'new-invoice'   => 'Nouvelle facture',
        'new-bill'      => 'Nouvelle facture fournisseur',
        'new-quotation' => 'Nouveau devis',
        'new-rfq'       => 'Nouvelle demande de prix',
    ],

    'cash' => [
        'heading'     => 'Entrées et sorties d\'argent',
        'description' => 'Factures encaissées et factures fournisseur payées par jour',
        'in'          => 'Entrées',
        'out'         => 'Sorties',
    ],

    'trend' => ':change par rapport à la période précédente',

    'modules' => [
        'heading' => 'Dans toute l\'entreprise',
        'open'    => 'Ouvrir',
    ],
];
