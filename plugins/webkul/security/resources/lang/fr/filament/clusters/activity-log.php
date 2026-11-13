<?php

return [
    'title'           => 'Journal d\'activité',
    'navigation'      => 'Journal d\'activité',
    'group'           => 'Général',
    'system'          => 'Système',
    'export'          => 'Exporter',
    'export-complete' => 'L\'export du journal d\'activité est prêt : :count lignes.',

    'columns' => [
        'when'    => 'Quand',
        'who'     => 'Qui',
        'record'  => 'Enregistrement',
        'action'  => 'Action',
        'changes' => 'Modifications',
    ],

    'filters' => [
        'record-type' => 'Type d\'enregistrement',
        'from'        => 'Du',
        'until'       => 'Au',
    ],

    'events' => [
        'created'      => 'Créé',
        'updated'      => 'Modifié',
        'deleted'      => 'Supprimé',
        'soft_deleted' => 'Archivé',
        'hard_deleted' => 'Supprimé',
        'restored'     => 'Restauré',
        'reset'        => 'Remis en brouillon',
        'cancelled'    => 'Annulé',
    ],
];
