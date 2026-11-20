<?php

return [
    'navigation' => [
        'label' => 'Jetons API',
    ],

    'group'      => 'Général',
    'breadcrumb' => 'Jetons API',
    'title'      => 'Jetons API',
    'subheading' => "Jetons utilisés par les intégrations clientes pour s'authentifier auprès de l'API. Un jeton hérite des rôles et des sociétés autorisées de l'utilisateur auquel il appartient.",

    'abilities' => [
        'read'  => 'Lecture seule',
        'write' => 'Écriture',
        'all'   => 'Accès complet',
    ],

    'table' => [
        'columns' => [
            'name'      => 'Libellé',
            'user'      => 'Agit en tant que',
            'abilities' => 'Portées',
            'last-used' => 'Dernière utilisation',
            'expires'   => 'Expire le',
            'created'   => 'Émis le',
        ],

        'never-used' => 'Jamais utilisé',
    ],

    'actions' => [
        'issue' => [
            'label'  => 'Émettre un jeton',
            'submit' => 'Émettre un jeton',

            'fields' => [
                'user'             => "Agit en tant qu'utilisateur",
                'user-helper'      => 'Le jeton peut faire exactement ce que cet utilisateur peut faire. Utilisez un utilisateur dédié par client, jamais un administrateur.',
                'name'             => 'Libellé',
                'name-helper'      => 'Comment vous reconnaîtrez ce jeton plus tard, par exemple acme-warehouse-sync.',
                'abilities'        => 'Portées',
                'abilities-helper' => "La lecture seule n'autorise ni POST, ni PATCH, ni DELETE. L'accès complet ignore toute vérification de portée.",
            ],
        ],

        'revoke' => [
            'label'       => 'Révoquer',
            'heading'     => 'Révoquer ce jeton ?',
            'description' => "L'intégration qui l'utilise cessera de fonctionner immédiatement. Cette action est irréversible.",
            'bulk'        => 'Révoquer la sélection',
        ],
    ],

    'notifications' => [
        'issued' => [
            'title' => 'Jeton émis',
            'body'  => 'Copiez-le maintenant : il ne pourra plus être affiché.',
        ],

        'revoked'      => 'Jeton révoqué',
        'missing-user' => "Cet utilisateur n'existe plus.",
    ],

    'plaintext' => [
        'title'       => 'Copiez ce jeton maintenant',
        'description' => "Il n'est stocké que sous forme de hachage : c'est la seule fois où il peut être affiché. Transmettez-le au client par un canal de confiance.",
    ],

    'empty' => [
        'heading'     => 'Aucun jeton API pour le moment',
        'description' => "Émettez-en un pour permettre à une intégration cliente de s'authentifier auprès de l'API.",
    ],
];
