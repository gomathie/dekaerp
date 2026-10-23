<?php

return [
    'administrator' => [
        'title'       => 'Administrateur',
        'description' => 'La personne qui administre cette société et gère ses propres utilisateurs.',
        'none'        => 'Aucun administrateur pour cette société pour le moment.',
        'pending'     => 'invitation envoyée, pas encore acceptée',
        'inactive'    => 'désactivé',
        'manage'      => 'Gérer cet utilisateur',

        'fields' => [
            'email'        => 'E-mail de l’administrateur',
            'email-helper' => 'Une invitation est envoyée à cette adresse, où la personne définit son nom et son mot de passe. Laissez vide pour utiliser l’e-mail de la société.',
        ],
    ],
];
