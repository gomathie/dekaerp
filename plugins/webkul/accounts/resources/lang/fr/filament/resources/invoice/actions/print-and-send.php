<?php

return [
    'title' => 'Imprimer et envoyer',

    'modal' => [
        'title' => 'Aperçu de la facture',

        'form' => [
            'partners'    => 'Client',
            'subject'     => 'Objet',
            'description' => 'Description',
            'files'       => 'Pièce jointe',
        ],

        'action' => [
            'submit' => [
                'title' => 'Envoyer',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Facture envoyée',
                'body'  => 'La facture a été envoyée avec succès.',
            ],
        ],
    ],

    'message' => [
        'subject'   => 'Facture :partner (Réf. :number)',
        'greeting'  => 'Bonjour :name,',
        'body'      => 'Votre facture :number de :company d’un montant de :amount est disponible. Merci de procéder au paiement dans les meilleurs délais.',
        'bank'      => 'Lors du paiement, merci d’indiquer la référence :number pour le compte :account.',
        'questions' => 'Pour toute question, n’hésitez pas à nous contacter.',
        'regards'   => 'Cordialement,',
    ],
];
