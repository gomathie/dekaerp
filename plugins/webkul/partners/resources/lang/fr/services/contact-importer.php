<?php

return [
    'errors' => [
        'empty'           => 'Le fichier ne contient aucune ligne.',
        'no-columns'      => 'Aucune colonne reconnue dans la ligne d’en-tête. Colonnes attendues : :columns.',
        'missing-name'    => 'Le fichier doit comporter une colonne « name ».',
        'max-rows'        => 'Seules les :max premières lignes ont été importées. Divisez le fichier et importez le reste.',
        'row'             => 'Ligne :row : :reason',
        'formula'         => 'Ligne :row : la valeur « :column » commence par un caractère qu’un tableur interprète comme une formule. Supprimez-le et importez à nouveau.',
        'duplicate-email' => 'Ligne :row : un contact avec l’adresse :email existe déjà.',
        'unknown-country' => 'Ligne :row : aucun pays ne correspond à « :country ».',
        'unknown-state'   => 'Ligne :row : aucune région ne correspond à « :state ».',
        'unknown-parent'  => 'Ligne :row : aucun contact existant ne correspond à la société « :parent ».',
    ],
];
