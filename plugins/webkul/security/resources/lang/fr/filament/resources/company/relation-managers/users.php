<?php

return [
    'title' => 'Utilisateurs et identifiants',

    'table' => [
        'columns' => [
            'name'                 => 'Nom',
            'login'                => 'Identifiant (e-mail)',
            'company-admin'        => 'Administrateur de la société',
            'active'               => 'Actif',
            'must-change-password' => 'Doit changer le mot de passe',
        ],

        'empty-state' => [
            'heading'     => 'Aucun utilisateur pour le moment',
            'description' => 'Invitez ou créez l’administrateur dans la section Administrateur ci-dessus.',
        ],

        'actions' => [
            'copy-invitation-link' => [
                'label'      => 'Copier le lien d’invitation',
                'invitation' => 'Invitation en attente',
                'link'       => 'Lien d’invitation',
            ],
            'set-password' => [
                'label'             => 'Définir le mot de passe',
                'modal-heading'     => 'Définir le mot de passe de :email',
                'modal-description' => 'Les mots de passe actuels ne peuvent pas être affichés, car ils sont chiffrés de manière irréversible. Définissez-en un nouveau et transmettez-le vous-même à l’utilisateur : il n’est pas envoyé par e-mail.',

                'form' => [
                    'password'             => 'Nouveau mot de passe',
                    'must-change-password' => 'Doit changer ce mot de passe à la prochaine connexion',
                ],
            ],
        ],
    ],
];
