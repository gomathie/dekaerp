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
            'email'                => 'E-mail de l’administrateur',
            'email-helper'         => 'Sans mot de passe, une invitation est envoyée à cette adresse et la personne choisit son propre mot de passe. Laissez vide pour inviter l’e-mail de la société.',
            'email-helper-edit'    => 'Saisissez une adresse pour inviter un nouvel administrateur. Ajoutez un mot de passe pour créer son compte immédiatement, ou pour réinitialiser le mot de passe de l’administrateur de cette société. Laissez vide pour ne rien changer.',
            'password'             => 'Mot de passe de l’administrateur',
            'password-helper'      => 'Facultatif. Définit le mot de passe maintenant au lieu d’envoyer une invitation, ou le réinitialise pour l’administrateur de cette société. Il n’est jamais envoyé par e-mail : transmettez-le vous-même.',
            'must-change-password' => 'Doit changer ce mot de passe à la prochaine connexion',
        ],
    ],
];
