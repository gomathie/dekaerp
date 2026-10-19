<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'Marquer comme enlevée',
            'notification'         => 'Expédition marquée comme enlevée',
            'transit-label'        => 'Démarrer le transit',
            'transit-notification' => 'Expédition marquée en transit',
        ],
        'deliver' => [
            'label'                         => 'Livrer l’expédition',
            'notification'                  => 'Expédition livrée',
            'stop-notification'             => 'Arrêt de livraison enregistré',
            'out-for-delivery-label'        => 'Marquer en cours de livraison',
            'out-for-delivery-notification' => 'Expédition marquée en cours de livraison',
            'fields'                        => [
                'capture-pod'    => 'Enregistrer la preuve de livraison',
                'stop'           => 'Arrêt de livraison',
                'recipient-name' => 'Nom du destinataire',
                'received-at'    => 'Reçu le',
                'reference'      => 'Référence',
                'notes'          => 'Notes',
                'photo'          => 'Photo de la livraison',
                'signature'      => 'Signature du destinataire',
            ],
        ],
        'fail' => [
            'label'               => 'Enregistrer l’échec de livraison',
            'reason'              => 'Motif',
            'notification'        => 'Échec de livraison enregistré',
            'resolve-label'       => 'Résoudre l’échec de livraison',
            'resolution'          => 'Étape suivante',
            'retry-notification'  => 'Expédition prête pour une nouvelle tentative de livraison',
            'return-notification' => 'Expédition marquée comme retournée',
            'resolutions'         => [
                'retry'  => 'Réessayer la livraison',
                'return' => 'Marquer comme retournée',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'Preuve de livraison',
        'columns' => [
            'recipient'    => 'Destinataire',
            'received-at'  => 'Reçu le',
            'stop'         => 'Arrêt',
            'captured-via' => 'Mode de saisie',
            'photo'        => 'Photo',
            'signature'    => 'Signature',
        ],
    ],
    'validation' => [
        'pod-required'        => 'Une preuve de livraison est requise avant de pouvoir marquer cette expédition comme livrée.',
        'stop-required'       => 'Sélectionnez l’arrêt de livraison auquel cette preuve appartient.',
        'stop-unavailable'    => 'L’arrêt de livraison sélectionné n’est plus disponible.',
        'upload-failed'       => 'Le fichier de preuve n’a pas pu être stocké. Veuillez réessayer.',
        'resolution-required' => 'Choisissez de réessayer ou de retourner l’expédition dont la livraison a échoué.',
    ],
];
