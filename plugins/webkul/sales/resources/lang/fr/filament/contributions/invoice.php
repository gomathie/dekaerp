<?php

return [
    'create-sale-order' => [
        'label'  => 'Créer une commande client',
        'helper' => 'À la confirmation de la facture, une commande client confirmée est créée à partir d’elle, avec livraison pour les produits stockés, afin qu’elle apparaisse dans Ventes et Inventaire.',
    ],

    'linked-order' => [
        'label' => 'Commande client',
    ],
    'goods-delivered-now' => [
        'label'  => 'Le client a emporté les marchandises',
        'helper' => 'Enregistre la livraison comme faite à la confirmation de la facture. Décochez pour la laisser à l’entrepôt.',
    ],

    'delivery' => [
        'label'               => 'Livraison',
        'waiting'             => 'En attente de l’entrepôt',
        'done'                => 'Livrée',
        'mark-delivered'      => 'Marquer comme livrée',
        'confirm-heading'     => 'Enregistrer la livraison ?',
        'confirm-description' => 'Les articles de cette facture seront enregistrés comme livrés et sortis du stock. Les articles sans stock suffisant restent en attente.',
        'delivered'           => 'Livraison enregistrée.',
        'not-enough-stock'    => 'Certains articles ne sont pas en stock ; ils restent en attente dans Inventaire.',
    ],

    'delete' => [
        'question'  => '{1} Annuler aussi la commande client :orders et sa livraison ?|[2,*] Annuler aussi les commandes client :orders et leurs livraisons ?',
        'yes'       => 'Oui, annuler',
        'no'        => 'Non, la garder à facturer plus tard',
        'stays'     => '{1} La commande client :orders est conservée et repasse « À facturer ».|[2,*] Les commandes client :orders sont conservées et repassent « À facturer ».',
        'cancelled' => 'Annulées : :orders.',
    ],
];
