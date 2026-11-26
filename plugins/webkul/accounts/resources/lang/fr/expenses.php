<?php

return [
    'errors' => [
        'date'            => 'Saisissez la date à laquelle la dépense a été payée.',
        'amount'          => 'Saisissez un montant supérieur à zéro.',
        'journal'         => 'Choisissez un compte bancaire ou de caisse de cette société.',
        'account'         => 'Choisissez un compte de charges de cette société.',
        'tax'             => 'Choisissez une taxe d\'achat de cette société.',
        'vendor'          => 'Choisissez un fournisseur de cette société.',
        'vendor-name'     => 'Saisissez le nom du fournisseur (255 caractères au plus).',
        'currency'        => 'Choisissez une devise que cette société peut utiliser.',
        'no-rate'         => 'Il n\'y a pas de taux de change pour :currency à cette date. Ajoutez-en un dans la configuration de la comptabilité, ou enregistrez la dépense dans la devise de la société.',
        'receipt'         => 'Le justificatif n\'a pas pu être accepté. Téléversez-le à nouveau.',
        'reference'       => 'La référence peut avoir 255 caractères au plus.',
        'reason-required' => 'Indiquez un motif : cette dépense est déjà en comptabilité.',
    ],
];
