<?php

return [
    'actions' => [
        'clock-in'  => 'Registrar entrada',
        'clock-out' => 'Registrar salida',
        'correct'   => 'Corregir asistencia',
        'export'    => 'Exportar asistencia',
    ],
    'capture' => [
        'off'      => 'Desactivada',
        'optional' => 'Opcional',
        'required' => 'Obligatoria',
    ],
    'exceptions' => [
        'not-enabled' => 'La asistencia no está activada para la empresa :company.',
    ],
    'notifications' => [
        'failed' => 'No se pudo completar. El cambio no se guardó.',
    ],
    'statuses' => [
        'clocked-in'     => 'Fichado desde :time',
        'not-clocked-in' => 'Sin fichar',
        'on-leave'       => 'De permiso',
    ],
    'validation' => [
        'check-out-order'     => 'La salida debe ser posterior a la entrada.',
        'location-required'   => 'Se requiere una ubicación para fichar.',
        'no-employee'         => 'No tiene ficha de empleado en esta empresa.',
        'not-allowed'         => 'Solo puede corregir la asistencia de su equipo.',
        'not-clocked-in'      => 'No ha fichado.',
        'not-your-attendance' => 'Solo puede fichar por usted mismo.',
        'reason-required'     => 'Una corrección necesita un motivo.',
    ],
];
