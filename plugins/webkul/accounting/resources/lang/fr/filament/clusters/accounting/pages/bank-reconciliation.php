<?php

return [
    'navigation' => [
        'title' => 'Rapprochement bancaire',
    ],

    'import' => [
        'label'                => 'Importer un relevé',
        'description'          => 'Téléversez le fichier CSV de votre banque. Indiquez le numéro de chaque colonne, en comptant à partir de 1 à gauche. Chaque compte bancaire retient ses colonnes pour la prochaine fois ; si la banque déplace une colonne, elle est retrouvée par son titre.',
        'journal'              => 'Compte bancaire',
        'file'                 => 'Fichier du relevé (CSV)',
        'name'                 => 'Nom du relevé',
        'date-format'          => 'Format de date',
        'has-header'           => 'La première ligne est un en-tête',
        'amount-help'          => 'Ou laissez vide et indiquez les colonnes entrées et sorties.',
        'balance-help'         => 'Facultatif : le solde courant. Sa valeur du dernier jour est le solde de clôture.',
        'closing-balance'      => 'Solde de clôture du relevé',
        'closing-balance-help' => 'Facultatif. Sans lui ni colonne de solde, le solde de clôture est celui du relevé précédent plus ces lignes.',
        'done'                 => ':count lignes importées.',

        'columns' => [
            'date'      => 'Colonne date',
            'label'     => 'Colonne libellé',
            'amount'    => 'Colonne montant',
            'money_in'  => 'Colonne entrées',
            'money_out' => 'Colonne sorties',
            'partner'   => 'Colonne client ou fournisseur',
            'reference' => 'Colonne référence',
            'balance'   => 'Colonne solde',
        ],
    ],

    'match-all' => [
        'label'       => 'Rapprocher toutes les suggestions (:count)',
        'heading'     => 'Rapprocher toutes les lignes suggérées ?',
        'description' => '{0} Aucune ligne n’a actuellement un seul paiement suggéré.|{1} :count ligne a un seul paiement suggéré du même montant. Elle y sera rapprochée. Les autres lignes restent telles quelles.|[2,*] :count lignes ont un seul paiement suggéré du même montant. Chacune sera rapprochée de son paiement. Les autres lignes restent telles quelles.',
        'submit'      => 'Rapprocher',
        'done'        => '{0} Aucune ligne rapprochée.|{1} :count ligne rapprochée.|[2,*] :count lignes rapprochées.',
        'left'        => '{1} :count ligne est restée ouverte : sa période est clôturée ou elle a changé entre-temps.|[2,*] :count lignes sont restées ouvertes : leur période est clôturée ou elles ont changé entre-temps.',
    ],

    'balances' => [
        'heading'      => 'Contrôle du solde par compte',
        'statement'    => 'Solde du relevé',
        'book'         => 'Solde comptable',
        'difference'   => 'Écart',
        'up-to'        => 'Relevé jusqu’au :date',
        'no-statement' => 'Aucun relevé importé',
        'explain'      => 'Solde du relevé : le solde de clôture du dernier relevé importé. Solde comptable : ce que le grand livre porte sur le compte ; un paiement compte une fois rapproché d’une ligne de relevé.',

        'state' => [
            'clean'      => 'À jour',
            'to-match'   => '{1} :count à rapprocher|[2,*] :count à rapprocher',
            'difference' => 'Écart à vérifier',
            'none'       => 'Aucun relevé',
        ],
    ],

    'columns' => [
        'date'    => 'Date',
        'label'   => 'Libellé',
        'partner' => 'Client ou fournisseur',
        'journal' => 'Compte bancaire',
        'amount'  => 'Montant',
        'status'  => 'Statut',
        'payment' => 'Paiement',
    ],

    'status' => [
        'reconciled'   => 'Rapprochée',
        'to-reconcile' => 'À rapprocher',
    ],

    'match' => [
        'label'       => 'Rapprocher un paiement',
        'description' => 'Paiements de ce compte bancaire du même montant, en attente de la banque.',
        'payment'     => 'Paiement',
        'none'        => 'Aucun paiement ne correspond. Enregistrez d’abord le paiement sur la facture, ou comptabilisez la ligne sur un compte.',
        'done'        => 'Ligne rapprochée. Le paiement est maintenant payé.',
    ],

    'book' => [
        'label'       => 'Comptabiliser sur un compte',
        'description' => 'Pour les frais bancaires, intérêts ou virements : passe la ligne sur le compte choisi.',
        'account'     => 'Compte',
        'entry-label' => 'Libellé',
        'done'        => 'Ligne comptabilisée.',
    ],

    'empty' => [
        'heading'     => 'Rien à rapprocher',
        'description' => 'Importez un relevé de votre banque pour commencer.',
    ],
];
