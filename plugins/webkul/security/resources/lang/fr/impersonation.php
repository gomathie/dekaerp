<?php

return [
    'banner' => [
        'message' => 'Vous êtes connecté en tant que :target (:company). Usurpation par :impersonator.',
        'ends-at' => 'Fin à :time',
        'end'     => 'Terminer l’usurpation',
    ],

    'notifications' => [
        'started' => 'Vous êtes maintenant connecté en tant que :name.',
        'refused' => 'L’usurpation n’a pas été lancée',

        'ended-automatically' => [
            'expired' => 'L’usurpation a atteint sa durée maximale et a pris fin.',
            'invalid' => 'L’usurpation n’est plus autorisée et a pris fin.',
        ],

        'granted' => 'Accès à l’usurpation accordé.',
        'revoked' => 'Accès à l’usurpation retiré.',
    ],

    'actions' => [
        'impersonate' => [
            'label'             => 'Se connecter en tant que',
            'modal-heading'     => 'Se connecter en tant que :name',
            'modal-description' => 'Vous agirez en tant que cet utilisateur, avec ses seules autorisations, pendant :minutes minutes au maximum. Tout ce que vous faites est enregistré à votre nom.',
            'submit'            => 'Commencer',

            'form' => [
                'reason'   => 'Motif',
                'password' => 'Votre mot de passe',
            ],
        ],

        'grant' => [
            'label'             => 'Autoriser l’usurpation',
            'modal-description' => 'Cet administrateur multi-sociétés pourra se connecter en tant qu’utilisateur des sociétés qui lui sont attribuées.',
        ],

        'revoke' => [
            'label'             => 'Retirer l’accès à l’usurpation',
            'modal-description' => 'Une usurpation en cours prend fin au prochain chargement de page.',
        ],
    ],
];
