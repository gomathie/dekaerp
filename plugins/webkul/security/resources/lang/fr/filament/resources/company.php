<?php

return [
    'title' => 'Sociétés',

    'navigation' => [
        'title' => 'Sociétés',
    ],

    'global-search' => [
        'email' => 'E-mail',
    ],

    'pages' => [
        'create' => [
            'administrator' => [
                'invited' => [
                    'title' => 'Administrateur invité',
                    'body'  => 'Une invitation a été envoyée à :email. Cette personne choisit son propre mot de passe, puis peut ajouter les utilisateurs de sa société.',
                ],

                'skipped' => [
                    'title'         => 'Aucun administrateur n’a été invité',
                    'missing-email' => 'La société a été créée, mais elle n’a pas d’adresse e-mail : personne n’a donc pu être invité à l’administrer. Ajoutez-en une et envoyez une invitation depuis la page Utilisateurs.',
                    'missing-role'  => 'La société a été créée, mais le rôle « Company Admin » n’existe pas dans cette installation : personne n’a pu être invité.',

                    'seats-exhausted' => 'L’entreprise a été créée, mais elle n’a plus de siège utilisateur disponible : personne n’a donc pu être invité à l’administrer. Augmentez la limite de sièges puis envoyez une invitation depuis la page Utilisateurs.',
                    'invite-failed'   => 'La société a été créée, mais l’invitation n’a pas pu être émise. L’adresse appartient peut-être déjà à un utilisateur, ou la société ne fait pas encore partie de celles que vous pouvez attribuer. Invitez depuis la page Utilisateurs.',
                    'mail-failed'     => 'La société a été créée et l’invitation émise, mais l’e-mail n’a pas pu être envoyé. Transmettez ce lien de connexion à :email par un canal de confiance — il expire dans 7 jours et la personne choisit son propre mot de passe : :link',
                ],
            ],
        ],
    ],
];
