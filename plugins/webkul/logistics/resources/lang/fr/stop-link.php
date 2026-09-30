<?php

return [
    'title'    => 'Confirmer la livraison',
    'shipment' => 'Expédition',
    'stop'     => 'Arrêt',

    'ttl' => [
        '4'  => '4 heures',
        '8'  => '8 heures',
        '16' => '16 heures',
        '24' => '24 heures (1 jour)',
        '48' => '48 heures (2 jours)',
    ],

    'form' => [
        'recipient-name'      => 'Reçu par',
        'recipient-name-help' => 'Le nom de la personne qui a réceptionné la livraison.',
        'recipient-id'        => 'Identifiant du destinataire',
        'recipient-id-help'   => 'Un identifiant ou une référence de la personne qui réceptionne, selon les exigences de votre société.',
        'photo'               => 'Photo',
        'photo-help'          => 'Une photo des marchandises livrées, de la porte ou de la lettre de voiture signée.',
        'signature'           => 'Signature',
        'signature-help'      => 'Demandez au destinataire de signer dans le cadre.',
        'signature-clear'     => 'Effacer',
        'notes'               => 'Notes',
        'location'            => 'Joindre ma position',
        'location-attached'   => 'Position jointe.',
        'location-failed'     => 'Position indisponible. Vous pouvez tout de même envoyer le formulaire.',
        'submit'              => 'Confirmer la livraison',
        'submitting'          => 'Confirmation…',
    ],

    'done' => [
        'title'   => 'Livraison confirmée',
        'message' => 'Merci. La livraison a été enregistrée et ce lien est désormais fermé.',
    ],

    'expired' => [
        'title'   => 'Ce lien n’est plus valide',
        'message' => 'Il a peut-être expiré, déjà été utilisé ou été retiré. Demandez un nouveau lien au bureau.',
    ],

    'actions' => [
        'revoke-pod-link' => [
            'label'        => 'Annuler le lien de preuve',
            'heading'      => 'Annuler le lien de preuve de livraison ?',
            'description'  => 'Tout lien déjà envoyé pour cette expédition cessera immédiatement de fonctionner. Utilisez cette option si un lien a été envoyé au mauvais numéro.',
            'notification' => '{0}Aucun lien actif n’était à annuler.|{1}Lien annulé.|[2,*]:count liens annulés.',
        ],
        'send-pod-link' => [
            'label'        => 'Lien de preuve',
            'heading'      => 'Lien de preuve de livraison à usage unique',
            'description'  => 'Partagez ce lien avec le chauffeur. Il fonctionne une seule fois, uniquement pour cet arrêt, et expire après :hours heures. La création d’un nouveau lien annule tout lien antérieur.',
            'copy'         => 'Copier le lien',
            'no-stop'      => 'Cette expédition n’a plus d’arrêt de livraison ouvert ; il n’y a donc rien à enregistrer.',
            'notification' => 'Lien créé.',
        ],
    ],
];
