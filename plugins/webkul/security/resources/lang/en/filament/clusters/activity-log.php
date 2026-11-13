<?php

return [
    'title'           => 'Activity log',
    'navigation'      => 'Activity log',
    'group'           => 'General',
    'system'          => 'System',
    'export'          => 'Export',
    'export-complete' => 'Your activity log export is ready: :count rows.',

    'columns' => [
        'when'    => 'When',
        'who'     => 'Who',
        'record'  => 'Record',
        'action'  => 'Action',
        'changes' => 'Changes',
    ],

    'filters' => [
        'record-type' => 'Record type',
        'from'        => 'From',
        'until'       => 'Until',
    ],

    'events' => [
        'created'      => 'Created',
        'updated'      => 'Changed',
        'deleted'      => 'Deleted',
        'soft_deleted' => 'Archived',
        'hard_deleted' => 'Deleted',
        'restored'     => 'Restored',
        'reset'        => 'Reset to draft',
        'cancelled'    => 'Cancelled',
    ],
];
