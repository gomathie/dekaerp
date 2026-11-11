<?php

return [
    'navigation' => [
        'label' => 'Aide',
    ],

    'title'      => 'Aide',
    'heading'    => 'Aide et ressources',
    'subheading' => 'Tout ce dont vous avez besoin pour tirer le meilleur parti de :app.',

    'guides' => [
        'group' => 'Guides',

        'superadmin' => [
            'title'       => 'Guide du super-administrateur',
            'description' => "Administrez l'installation :app elle-même : modules, entreprises, rôles d'administrateur, mises à niveau et maintenance.",
            'button'      => 'Ouvrir le guide du super-administrateur',
        ],

        'admin' => [
            'title'       => "Guide de l'administrateur",
            'description' => 'Configurez :app pour votre entreprise, vos équipes, vos modules et vos contrôles avant le début du travail quotidien.',
            'button'      => 'Liste de configuration administrateur',
            'sections'    => [
                [
                    'title' => "Configurer l'entreprise",
                    'items' => [
                        '0' => 'Créez ou vérifiez les entreprises, entrepôts, devises, taxes et paramètres fiscaux.',
                        '1' => "Affectez les utilisateurs aux entreprises auxquelles ils ont accès et vérifiez le sélecteur d'entreprise active.",
                        '2' => 'Configurez les séquences, conditions de paiement, produits, unités de mesure et modèles de documents avant le début des transactions.',
                    ],
                ],
                [
                    'title' => "Contrôler l'accès",
                    'items' => [
                        '0' => 'Créez des rôles pour les équipes finance, ventes, stock, RH, projets et direction.',
                        '1' => "N'accordez les autorisations de module qu'aux utilisateurs qui en ont besoin.",
                        '2' => "Vérifiez les champs personnalisés, imports, exports et jetons d'API avant d'accorder un accès large.",
                    ],
                ],
                [
                    'title' => 'Exploiter et maintenir',
                    'items' => [
                        '0' => "Installez uniquement les modules utilisés par l'entreprise, puis masquez ou supprimez les autres.",
                        '1' => 'Suivez les discussions, pièces jointes, approbations et propriétaires des enregistrements pendant le déploiement.',
                        '2' => "Alignez les paramètres d'e-mail, de PDF, de sauvegarde et de mise à jour sur la politique de déploiement.",
                    ],
                ],
            ],
        ],

        'user' => [
            'title'          => "Guide de l'utilisateur",
            'description'    => 'Utilisez le guide intégré pour les présentations DEKA et les pages de référence des modules pour le travail quotidien dans :app.',
            'button'         => "Ouvrir le guide de l'utilisateur",
            'full_heading'   => "Guide complet de l'utilisateur",
            'count'          => '{0} Aucune page de guide|{1} 1 page de guide|[2,*] :count pages de guide',
            'category_count' => '{0} Aucune page|{1} 1 page|[2,*] :count pages',
            'callouts'       => [
                'tip'       => 'Astuce',
                'note'      => 'Remarque',
                'important' => 'Important',
            ],
            'sections'    => [
                [
                    'title' => 'Premiers pas',
                    'items' => [
                        '0' => 'Présentation de la plateforme et navigation',
                        '1' => "Configuration multi-entreprise et sélecteur d'entreprise",
                    ],
                ],
                [
                    'title' => 'Processus principaux',
                    'items' => [
                        '0' => 'Vendre : ventes et facturation',
                        '1' => 'Acheter : achats et comptabilité',
                        '2' => 'Produire et déplacer : stock et production',
                        '3' => 'Piloter : projets, RH et maintenance',
                        '4' => 'Plateforme, API et auto-hébergement',
                    ],
                ],
                [
                    'title' => 'Référence des modules : 12 modules / 69 pages',
                    'items' => [
                        '0' => 'Factures, Ventes, Achats, Stock, Fabrication, Maintenance',
                        '1' => 'Contacts, Projets, Employés, Recrutement, Congés',
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'group' => 'Services',

        'cloud' => [
            'title'       => 'Hébergement cloud',
            'description' => 'Hébergement cloud géré et économique — déployez votre ERP en quelques minutes, entièrement optimisé, sécurisé et évolutif.',
            'button'      => 'Voir les tarifs',
        ],

        'support' => [
            'title'       => 'Support et maintenance',
            'description' => 'Support technique dédié et plans de maintenance continue pour garder votre ERP sécurisé, à jour et fonctionnant correctement.',
            'button'      => 'Voir les détails',
        ],

        'paid' => [
            'title'       => 'Services payants',
            'description' => 'Assistance experte pour le développement de modules, la personnalisation, la migration de données, les mises à niveau de version et les intégrations sur mesure.',
            'button'      => 'Commencer',
        ],
    ],

    'resources' => [
        'group' => 'Ressources et documentation',

        'extensions' => [
            'title'       => 'Modules',
            'description' => 'Parcourez les extensions officielles et communautaires pour étendre :app avec de nouveaux modules et fonctionnalités.',
            'button'      => 'Parcourir les modules',
        ],

        'docs' => [
            'title'       => 'Documentation technique',
            'description' => 'Guides et références pour développeurs afin de vous aider à créer des plugins, personnaliser des modules et étendre la plateforme.',
            'button'      => 'Lire la documentation',
        ],

        'guide' => [
            'title'       => "Guide de l'utilisateur",
            'description' => "Guides étape par étape couvrant l'installation, la configuration et l'utilisation quotidienne de chaque module ERP.",
            'button'      => 'Ouvrir le guide',
        ],

        'website' => [
            'title'       => 'Site du produit',
            'description' => 'Présentation des modules, notes de version et tout ce que :app peut faire, sur le site du produit.',
            'button'      => 'Visiter le site',
        ],
    ],

    'empty' => "Les liens d'aide n'ont pas encore été configurés pour cette installation de :app. Contactez votre administrateur système.",

    'contact' => [
        'title'       => "Besoin d'aide supplémentaire ?",
        'description' => "Discutez avec notre équipe de l'hébergement, de l'implémentation, du développement personnalisé ou de toute autre question.",
        'live_chat'   => 'Chat en direct',
        'button'      => 'Nous contacter',
    ],
];
