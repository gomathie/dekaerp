<?php

return [
    'title' => 'Contacts',

    'header-actions' => [
        'create' => [
            'label' => 'Créer un contact',
        ],

        'import' => [
            'label' => 'Importer depuis Excel',

            'modal' => [
                'heading'                 => 'Importer des contacts depuis un tableur',
                'description'             => 'Téléchargez le fichier d’exemple, saisissez un contact par ligne, puis déposez-le ici. Jusqu’à :max contacts par import.',
                'submit'                  => 'Importer les contacts',
                'download-template-csv'   => 'Télécharger l’exemple (CSV)',
                'download-template-excel' => 'Télécharger l’exemple (Excel)',

                'file' => [
                    'label'  => 'Fichier complété',
                    'helper' => 'Excel (.xlsx, .xls) ou CSV. Conservez la ligne d’en-tête telle quelle. Colonne obligatoire : :columns. Les lignes posant problème sont signalées par leur numéro et les autres sont importées.',
                ],
            ],

            'notifications' => [
                'imported'         => ':count contacts importés',
                'skipped'          => ':count lignes ont été ignorées :',
                'nothing-imported' => 'Aucun contact n’a été importé',
                'more-errors'      => '... et :count lignes de plus.',
                'unreadable'       => 'Ce fichier n’a pas pu être lu. Veuillez importer un fichier Excel ou CSV.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'Particuliers',
        'companies'   => 'Sociétés',
        'employees'   => 'Employés',
        'customers'   => 'Clients',
        'vendors'     => 'Fournisseurs',
        'archived'    => 'Archivés',
    ],
];
