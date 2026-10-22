<?php

return [

    'title' => 'Plugin',

    'table' => [
        'version'             => 'Version',
        'dependencies'        => 'Dépendances',
        'dependencies_suffix' => ' Dépendances',
    ],

    'status' => [
        'installed'     => 'Installé',
        'not_installed' => 'Non installé',
    ],

    'filters' => [
        'installation_status' => "Statut d'installation",
        'all_plugins'         => 'Tous les plugins',
        'installed'           => 'Installé',
        'not_installed'       => 'Non installé',
        'active_status'       => 'Statut actif',
        'author'              => 'Auteur',
        'webkul'              => 'Webkul',
        'third_party'         => 'Tiers',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Installer',
            'heading'     => 'Installer le plugin :name',
            'description' => "Êtes-vous sûr de vouloir installer le plugin ':name' ? Ceci exécutera les migrations et les seeders.",
            'submit'      => 'Installer le plugin',
        ],

        'companies' => [
            'title'       => 'Sociétés',
            'heading'     => 'Quelles sociétés peuvent utiliser :name ?',
            'description' => 'Installer un plugin rend son code disponible pour toute l’installation. Ceci décide quelles sociétés peuvent réellement l’utiliser : accordez-le aux sociétés dont l’abonnement l’inclut. Retirer une société masque le plugin pour tous ses utilisateurs.',
            'field'       => 'Sociétés',

            'notification' => [
                'title' => 'Disponibilité du plugin mise à jour',
                'body'  => ':name est maintenant disponible pour :count société(s).',
            ],
        ],
        'uninstall' => [
            'title'      => 'Désinstaller',
            'heading'    => 'Désinstaller le plugin',
            'submit'     => 'Désinstaller le plugin',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Plugin installé avec succès',
            'body'  => "Le plugin ':name' a été installé.",
        ],
        'installed-failed' => [
            'title' => "Échec de l'installation",
        ],
        'uninstalled' => [
            'title' => 'Plugin désinstallé avec succès',
            'body'  => "Le plugin ':name' a été désinstallé.",
        ],

        'cache-rebuild-failed' => [
            'title' => 'Les caches ont été vidés mais non reconstruits',
            'body'  => 'La modification du plugin a bien abouti. L’application fonctionne désormais sans cache de configuration ni de routes, ce qui est plus lent mais correct. Exécutez « php artisan optimize » et lisez sa sortie pour comprendre l’échec ; l’erreur a également été signalée.',
        ],
        'uninstalled-failed' => [
            'title' => 'Échec de la désinstallation',
        ],
        'uninstalled-blocked' => [
            'title' => 'Impossible de désinstaller le plugin',
            'body'  => "Le plugin ':name' possède des plugins dépendants installés qui doivent d'abord être désinstallés : :dependents.",
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Informations sur le plugin',
            'dependencies' => 'Dépendances',
        ],
        'name'         => 'Nom du plugin',
        'author'       => 'Auteur',
        'version'      => 'Version',
        'dependencies' => 'Plugins requis',
        'dependents'   => 'Plugins qui dépendent de celui-ci',
        'is_installed' => "Statut d'installation",
        'license'      => 'Licence',
        'summary'      => 'Description',

        'dependencies-repeater' => [
            'title'        => 'Plugins requis',
            'name'         => 'Nom du plugin',
            'is_installed' => 'Installé',
            'placeholder'  => 'Aucune dépendance requise',
        ],

        'dependents-repeater' => [
            'title'        => 'Plugins qui dépendent de celui-ci',
            'name'         => 'Nom du plugin',
            'is_installed' => 'Installé',
            'placeholder'  => 'Aucun dépendant',
        ],

    ],

    'names' => [
        'accounting'     => 'Comptabilité',
        'accounts'       => 'Comptes',
        'analytics'      => 'Analytique',
        'barcode'        => 'Code-barres',
        'blogs'          => 'Blogs',
        'chatter'        => 'Chatter',
        'contacts'       => 'Contacts',
        'employees'      => 'Employés',
        'fields'         => 'Champs personnalisés',
        'full-calendar'  => 'Calendrier',
        'inventories'    => 'Inventaire',
        'invoices'       => 'Factures',
        'maintenance'    => 'Maintenance',
        'manufacturing'  => 'Fabrication',
        'partners'       => 'Partenaires',
        'payments'       => 'Paiements',
        'plugin-manager' => 'Gestionnaire de plugins',
        'products'       => 'Produits',
        'projects'       => 'Projets',
        'purchases'      => 'Achats',
        'recruitments'   => 'Recrutement',
        'sales'          => 'Ventes',
        'security'       => 'Sécurité',
        'support'        => 'Support',
        'table-views'    => 'Vues de tableau',
        'time-off'       => 'Congés',
        'timesheets'     => 'Feuilles de temps',
        'website'        => 'Site web',
    ],

    'summaries' => [
        'accounting'     => 'Gérer le plan comptable, les journaux et les écritures financières',
        'accounts'       => 'Gestion des comptes de base et paramètres financiers',
        'analytics'      => "Rapports et tableaux de bord pour l'analyse d'activité",
        'barcode'        => "Application d'opérations de codes-barres pour l'inventaire et la fabrication",
        'blogs'          => 'Gérer les blogs',
        'chatter'        => "Journal d'activité, messagerie et suivi sur les enregistrements",
        'contacts'       => 'Gestion des contacts pour les clients et les fournisseurs',
        'employees'      => 'Gestion des employés',
        'fields'         => 'Ajouter des champs personnalisés aux ressources',
        'full-calendar'  => "Vues calendrier et planification d'événements",
        'inventories'    => "Gestion des stocks et de l'entrepôt",
        'invoices'       => 'Génération et gestion des factures',
        'maintenance'    => 'Gestion de la maintenance',
        'manufacturing'  => 'Gestion de la fabrication et de la production',
        'partners'       => 'Gérer les partenaires commerciaux',
        'payments'       => 'Gérer les paiements et les transactions',
        'plugin-manager' => 'Gestionnaire de plugins pour Aureus ERP',
        'products'       => 'Catalogue de produits et gestion des variantes',
        'projects'       => 'Planification et gestion de projets',
        'purchases'      => "Gestion des achats et des commandes d'approvisionnement",
        'recruitments'   => 'Suivi des candidatures et recrutement',
        'sales'          => 'Gestion du pipeline de ventes et des opportunités',
        'security'       => "Rôles, autorisations et contrôle d'accès",
        'support'        => 'Support client et gestion des tickets',
        'table-views'    => 'Vues de tableau enregistrées et personnalisables',
        'time-off'       => 'Gestion et suivi des congés',
        'timesheets'     => 'Suivi des heures de travail des employés',
        'website'        => 'Site web pour les clients',
    ],

];
