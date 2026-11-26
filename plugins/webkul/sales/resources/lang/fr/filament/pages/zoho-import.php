<?php

return [
    'navigation' => 'Importer depuis Zoho Books',
    'title'      => 'Importer depuis Zoho Books',
    'intro'      => 'Reprenez l’historique d’une société depuis Zoho Books : choisissez la société, chargez l’export, vérifiez la correspondance des comptes, taxes et comptes de trésorerie, puis importez. Les documents sont enregistrés du plus ancien au plus récent avec leurs numéros Zoho, puis la page compare Zoho et DEKA.',
    'empty'      => 'Aucun import pour l’instant. Commencez par « Charger un export Zoho ».',
    'unused'     => ':count comptes Zoho ne sont utilisés par aucun document et sont ignorés.',

    'propose' => [
        'label'       => 'Charger un export Zoho',
        'description' => 'Le fichier d’export (JSON, 10 Mo au plus) est vérifié et rapproché de la société. Rien n’est encore enregistré.',
        'company'     => 'Société',
        'file'        => 'Export Zoho Books (JSON)',
        'done'        => 'La correspondance est prête. Vérifiez-la ci-dessous, puis importez.',
    ],

    'propose-options' => [
        'numbering'      => 'Numérotation des nouveaux documents',
        'numbering-deka' => 'Série annuelle de DEKA (les documents importés gardent leurs numéros Zoho)',
        'numbering-zoho' => 'Continuer le compteur de Zoho',
        'skip-samples'   => 'Ignorer les contacts de démonstration Zoho (« (Sample) ») qu’aucun document n’utilise',
    ],

    'compare' => [
        'label'       => 'Comparer avec un nouvel export Zoho',
        'description' => 'Pour la période en parallèle : chargez un export Zoho récent pour le comparer à ce que DEKA contient, y compris ce qui a été saisi directement dans DEKA. Rien n’est enregistré.',
        'queued'      => 'La comparaison a commencé. Cette page en suit l’avancement.',
    ],

    'download' => [
        'label' => 'Télécharger la comparaison (CSV)',
    ],

    'unmatched' => [
        'none' => 'Chaque facture, paiement et dépense est dans les deux systèmes.',
    ],

    'modes' => [
        'import'  => 'Import',
        'compare' => 'Comparaison',
    ],

    'import' => [
        'label'       => 'Importer',
        'confirm'     => 'Importer dans :company ?',
        'description' => 'Tout l’historique est enregistré dans cette société. Cela ne peut pas être annulé ici ; un import interrompu reprend là où il s’est arrêté quand il est relancé.',
        'queued'      => 'L’import a commencé. Cette page en suit l’avancement.',
    ],

    'run' => [
        'heading'    => 'Import dans :company',
        'status'     => 'Statut',
        'started-by' => 'Lancé par',
        'source'     => 'Dans l’export',
        'working'    => 'Import en cours. Cela peut prendre plusieurs minutes ; la page se met à jour seule.',
    ],

    'statuses' => [
        'proposed' => 'En attente de votre accord',
        'queued'   => 'En file d’attente',
        'running'  => 'Import en cours',
        'finished' => 'Terminé',
        'failed'   => 'Arrêté',
    ],

    'sections' => [
        'accounts'   => 'Comptes',
        'taxes'      => 'Taxes',
        'banks'      => 'Comptes bancaires et de caisse',
        'records'    => 'Produits, contacts et numérotation',
        'result'     => 'Ce qui a été enregistré',
        'unmatched'  => 'Documents présents dans un seul système',
        'comparison' => 'Zoho comparé à DEKA',
    ],

    'columns' => [
        'zoho'       => 'Zoho',
        'uses'       => 'Utilisations',
        'action'     => 'Action',
        'deka'       => 'DEKA',
        'section'    => 'Section',
        'row'        => 'Ligne',
        'difference' => 'Écart',
    ],

    'actions' => [
        'map'    => 'utiliser l’existant',
        'create' => 'créer',
        'unused' => 'non utilisé',
    ],

    'records' => [
        'items'          => 'Articles : :create à créer, :map rapprochés de produits existants.',
        'contacts'       => 'Contacts : :create à créer, :map rapprochés de contacts existants.',
        'numbering-deka' => 'Les nouveaux :kind suivent la série annuelle de DEKA ; les documents importés gardent leurs numéros Zoho.',
        'numbering'      => 'Les nouveaux :kind continuent avec :next.',
    ],

    'comparison' => [
        'all-ok'      => 'Toutes les lignes concordent.',
        'differences' => ':count lignes diffèrent.',
    ],

    'errors' => [
        'empty-file' => 'Le fichier chargé n’a pas pu être lu.',
        'no-run'     => 'Chargez d’abord un export.',
    ],
];
