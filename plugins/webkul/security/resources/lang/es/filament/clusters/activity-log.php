<?php

return [
    'title'           => 'Registro de actividad',
    'navigation'      => 'Registro de actividad',
    'group'           => 'General',
    'system'          => 'Sistema',
    'export'          => 'Exportar',
    'export-complete' => 'La exportación del registro de actividad está lista: :count filas.',

    'columns' => [
        'when'    => 'Cuándo',
        'who'     => 'Quién',
        'record'  => 'Registro',
        'action'  => 'Acción',
        'changes' => 'Cambios',
    ],

    'filters' => [
        'record-type' => 'Tipo de registro',
        'from'        => 'Desde',
        'until'       => 'Hasta',
    ],

    'events' => [
        'created'      => 'Creado',
        'updated'      => 'Modificado',
        'deleted'      => 'Eliminado',
        'soft_deleted' => 'Archivado',
        'hard_deleted' => 'Eliminado',
        'restored'     => 'Restaurado',
        'reset'        => 'Devuelto a borrador',
        'cancelled'    => 'Cancelado',
    ],
];
