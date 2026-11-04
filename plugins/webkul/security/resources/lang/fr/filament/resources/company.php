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

        'edit' => [
            'administrator' => [
                'skipped' => [
                    'title'           => 'Aucun administrateur n’a été invité',
                    'disabled'        => 'Les invitations sont désactivées dans les paramètres des utilisateurs : :email n’a donc pas été invité. Définissez plutôt un mot de passe, ou activez les invitations.',
                    'missing-role'    => 'Le rôle « Company Admin » n’existe pas dans cette installation : personne n’a pu être invité.',
                    'seats-exhausted' => 'Cette société n’a plus de siège utilisateur disponible : :email n’a donc pas été invité. Augmentez d’abord la limite de sièges.',
                    'invite-failed'   => 'L’invitation n’a pas pu être émise. L’adresse appartient peut-être déjà à un utilisateur, ou la société ne fait pas partie de celles que vous pouvez attribuer. Pour réinitialiser le mot de passe d’un administrateur existant, saisissez aussi un mot de passe.',
                    'mail-failed'     => 'L’invitation a été émise, mais l’e-mail n’a pas pu être envoyé. Transmettez ce lien de connexion à :email par un canal de confiance — il expire dans 7 jours et la personne choisit son propre mot de passe : :link',
                ],
            ],
        ],

        'administrator-account' => [
            'created' => [
                'title' => 'Compte administrateur créé',
                'body'  => ':email peut se connecter dès maintenant avec le mot de passe que vous avez défini. Il n’a pas été envoyé par e-mail : transmettez-le vous-même.',
            ],

            'password-reset' => [
                'title' => 'Mot de passe de l’administrateur modifié',
                'body'  => 'Le mot de passe de :email a été modifié. Il n’a pas été envoyé par e-mail : transmettez-le vous-même.',
            ],

            'refused' => [
                'title'             => 'L’administrateur n’a pas été modifié',
                'missing-email'     => 'Saisissez l’e-mail de l’administrateur avec le mot de passe.',
                'missing-role'      => 'Le rôle « Company Admin » n’existe pas dans cette installation : aucun compte n’a été créé.',
                'not-member'        => ':email appartient à un utilisateur qui n’est pas membre de cette société : son mot de passe n’a donc pas été modifié ici. Gérez cet utilisateur depuis la page Utilisateurs.',
                'not-allowed'       => 'Vous n’êtes pas autorisé à gérer l’administrateur de cette société.',
                'seats-exhausted'   => 'Cette société n’a plus de siège utilisateur disponible : aucun compte n’a été créé pour :email. Augmentez d’abord la limite de sièges.',
            ],
        ],
    ],
];
