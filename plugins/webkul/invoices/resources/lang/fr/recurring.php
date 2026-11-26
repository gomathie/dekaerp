<?php

return [
    'title'      => 'Facture récurrente',
    'navigation' => 'Factures récurrentes',

    'sections' => [
        'details' => 'Calendrier',
        'lines'   => 'Lignes de facture',
        'history' => 'Historique des factures',
    ],

    'fields' => [
        'name'         => 'Nom du modèle',
        'customer'     => 'Client',
        'journal'      => 'Journal des ventes',
        'currency'     => 'Devise',
        'responsible'  => 'Responsable',
        'payment-term' => 'Conditions de paiement',
        'next-date'    => 'Date de la prochaine facture',
        'frequency'    => 'Répéter',
        'ends-on'      => 'Date de fin',
        'auto-email'   => 'Confirmer et envoyer par e-mail automatiquement',
        'notes'        => 'Notes',
        'product'      => 'Produit ou service',
        'description'  => 'Description',
        'quantity'     => 'Quantité',
        'price'        => 'Prix unitaire',
        'discount'     => 'Remise (%)',
        'taxes'        => 'Taxes',
        'status'       => 'Statut',
        'last-issue'   => 'Dernier problème',
        'invoice'      => 'Facture',
        'period-start' => 'Début de période',
        'period-end'   => 'Fin de période',
    ],

    'frequencies' => [
        'weekly'    => 'Chaque semaine',
        'monthly'   => 'Chaque mois',
        'quarterly' => 'Chaque trimestre',
        'yearly'    => 'Chaque année',
    ],

    'actions' => [
        'create'       => 'Nouvelle facture récurrente',
        'add-line'     => 'Ajouter une ligne',
        'generate-now' => 'Créer la prochaine facture maintenant',
        'pause'        => 'Suspendre',
        'resume'       => 'Reprendre',
        'stop'         => 'Arrêter',
        'history'      => 'Historique',
    ],

    'errors' => [
        'inactive-customer' => 'Ce client est inactif. Mettez le client à jour avant de créer une facture.',
        'customer-email'    => 'Ajoutez une adresse e-mail à ce client avant d\'activer l\'envoi automatique.',
        'journal'           => 'Choisissez un journal des ventes actif de cette société.',
        'currency'          => 'Choisissez une devise disponible.',
        'lines'             => 'Ajoutez au moins une ligne de facture.',
        'locked-period'     => 'La date de la prochaine facture tombe dans une période comptable verrouillée.',
        'frequency'         => 'Choisissez une fréquence prise en charge.',
        'company-mismatch'  => 'Cette valeur doit appartenir à la société du modèle.',
        'responsible'       => 'Choisissez un utilisateur de cette société.',
        'state'             => 'Cette facture récurrente ne peut pas passer à ce statut.',
        'end-date'          => 'La date de fin doit être égale ou postérieure à la date de la prochaine facture.',
        'email-failed'      => 'La facture :number a été créée, mais l\'e-mail n\'a pas pu être envoyé. Ouvrez la facture et renvoyez-la.',
    ],

    'notifications' => [
        'created' => [
            'title' => 'Une facture récurrente est prête à vérifier',
            'body'  => 'Le brouillon de facture :number a été créé. Vérifiez-le et envoyez-le au client.',
            'view'  => 'Ouvrir la facture',
        ],
    ],
];
