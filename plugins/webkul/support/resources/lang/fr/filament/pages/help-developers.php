<?php

return [
    'tabs' => [
        'help'       => 'Aide',
        'developers' => 'Développeurs',
    ],

    'heading' => 'Référence de l’API',
    'intro'   => 'Connectez vos propres logiciels aux données de votre entreprise. Chaque requête agit au nom d’un utilisateur, avec un jeton, et ne voit que ce que cet utilisateur peut voir.',
    'general' => 'Général',
    'empty'   => 'Cette entreprise n’a encore aucun module doté d’une API. La connexion et la déconnexion fonctionnent pour toutes les entreprises.',
    'copy'    => 'Copier',
    'copied'  => 'Copié',

    'badges' => [
        'version'   => 'API :name v1',
        'total'     => '{0} Aucun point d’accès|{1} 1 point d’accès|[2,*] :count points d’accès',
        'in_module' => ':count dans :module',
    ],

    'modules' => [
        'label'       => 'Modules',
        'sales'       => 'Ventes',
        'purchases'   => 'Achats',
        'accounts'    => 'Comptabilité',
        'inventories' => 'Stock',
        'products'    => 'Produits',
        'partners'    => 'Contacts',
        'projects'    => 'Projets',
        'support'     => 'Paramètres',
    ],

    'search' => [
        'placeholder' => 'Rechercher dans :module',
        'empty'       => 'Aucun point d’accès ne correspond.',
    ],

    'getting_started' => [
        'title'               => 'Premiers pas',
        'base_url'            => 'URL de base',
        'authentication'      => 'Authentification',
        'authentication_help' => 'Envoyez votre jeton dans l’en-tête Authorization de chaque requête.',
        'token'               => 'Obtenir un jeton',
        'token_page'          => 'Créez-en un dans Paramètres › Jetons API, pour un utilisateur dédié à l’intégration.',
        'token_page_button'   => 'Ouvrir les jetons API',
        'token_login'         => 'Ou faites se connecter l’intégration avec POST /admin/api/v1/login (ci-dessous).',
        'scopes'              => 'Portées',
        'scopes_help'         => 'Un jeton read ne peut faire que des requêtes GET. Créer, modifier ou supprimer quoi que ce soit demande write.',
        'company'             => 'Quelle entreprise',
        'company_help'        => 'Envoyez X-Company-Id avec l’une des entreprises de l’utilisateur du jeton. Sans cet en-tête, les requêtes vont à son entreprise par défaut.',
        'company_current'     => 'L’entreprise dans laquelle vous travaillez a l’identifiant :id.',
        'limits'              => 'Limites',
        'rate_limit'          => '{1} 1 requête par minute et par jeton.|[2,*] :count requêtes par minute et par jeton.',
        'expiry'              => 'Les jetons expirent après :time.',
        'no_expiry'           => 'Les jetons n’expirent pas.',
        'format'              => 'Format',
        'format_help'         => 'Envoyez toujours cet en-tête, pour que les erreurs reviennent en JSON et non sous forme de page web.',
    ],

    'full_reference' => [
        'title'       => 'Référence complète',
        'description' => 'Tous les modules et points d’accès, avec une console pour essayer les requêtes. S’ouvre dans un nouvel onglet et demande d’être connecté.',
        'button'      => 'Ouvrir la référence complète',
    ],

    'headers' => [
        'authorization' => 'Bearer suivi de votre jeton.',
        'company'       => 'L’identifiant de l’entreprise dans laquelle travailler.',
        'accept'        => 'application/json',
    ],

    'in' => [
        'path'   => 'chemin',
        'query'  => 'requête',
        'body'   => 'corps',
        'header' => 'en-tête',
    ],

    'endpoint' => [
        'parameters'    => 'Paramètres',
        'name'          => 'Nom',
        'type'          => 'Type',
        'description'   => 'Description',
        'required'      => 'Obligatoire',
        'optional'      => 'Facultatif',
        'samples'       => 'Exemples de requêtes',
        'response'      => 'Réponse (:status)',
        'other_answers' => 'Autres réponses :',
        'no_token'      => 'Sans jeton',
    ],

    'answers' => [
        '401' => '401 aucun jeton, ou un jeton expiré',
        '403' => '403 l’utilisateur ou l’entreprise du jeton n’y est pas autorisé',
        '422' => '422 les données envoyées ne sont pas valides, et errors nomme chaque champ',
        '429' => '429 trop de requêtes, attendez une minute',
    ],
];
