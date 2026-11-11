<?php

return [
    'breadcrumb' => 'Gérer les utilisateurs',
    'title'      => 'Gérer les utilisateurs',
    'group'      => 'Général',

    'navigation' => [
        'label' => 'Gérer les utilisateurs',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Activer l’invitation des utilisateurs',
            'helper-text' => 'Autoriser les utilisateurs à inviter d’autres utilisateurs dans l’application.',
        ],

        'enable-reset-password' => [
            'label'       => 'Activer la réinitialisation du mot de passe',
            'helper-text' => 'Autoriser les utilisateurs à réinitialiser leur mot de passe.',
        ],

        'default-role' => [
            'label'       => 'Rôle par défaut',
            'helper-text' => 'Le rôle par défaut attribué aux nouveaux utilisateurs.',
        ],
        'company-admin-business-role' => [
            'label'       => 'Rôle métier de l’administrateur d’entreprise',
            'helper-text' => 'Attribué à chaque nouvel administrateur d’entreprise en plus de Company Admin, pour qu’il puisse gérer son entreprise. Les rôles de la plateforme ne sont pas proposés.',
        ],

        'default-company' => [
            'label'       => 'Société par défaut',
            'helper-text' => 'La société par défaut attribuée aux nouveaux utilisateurs.',
        ],
    ],
];
