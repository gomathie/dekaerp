<?php

return [
    'navigation' => [
        'title' => 'Ajustes de asistencia',
    ],
    'title'      => 'Ajustes de asistencia',
    'subheading' => 'Asistencia de :company. Actívela aquí y los empleados podrán fichar desde el panel.',
    'no-company' => 'Elija una empresa para gestionar sus ajustes de asistencia.',
    'sections'   => [
        'status' => 'Estado',
        'rules'  => 'Reglas',
    ],
    'status' => [
        'enabled'  => 'Activada para esta empresa',
        'disabled' => 'Desactivada para esta empresa',
    ],
    'fields' => [
        'auto-checkout'         => 'Salida automática después de',
        'auto-checkout-help'    => 'Una salida olvidada se cierra tras estas horas y se marca para revisión.',
        'capture-location'      => 'Ubicación al fichar',
        'capture-location-help' => 'Desactivada: no se registra nada. Opcional: se registra si el dispositivo la comparte. Obligatoria: fichar la requiere.',
        'late-tolerance'        => 'Tarde después de',
        'overtime-tolerance'    => 'Horas extra después de',
    ],
    'units' => [
        'hours'   => 'horas',
        'minutes' => 'minutos',
    ],
    'actions' => [
        'save'   => 'Guardar',
        'enable' => [
            'label'        => 'Activar',
            'heading'      => '¿Activar la asistencia?',
            'description'  => 'Los empleados de esta empresa con ficha de empleado pueden fichar desde el panel. Los responsables ven a su equipo.',
            'notification' => 'La asistencia está activada para esta empresa.',
        ],
        'disable' => [
            'label'        => 'Desactivar',
            'heading'      => '¿Desactivar la asistencia?',
            'description'  => 'Se deja de fichar y se ocultan las pantallas de asistencia. Los registros existentes se conservan.',
            'notification' => 'La asistencia está desactivada para esta empresa.',
        ],
    ],
    'notifications' => [
        'saved' => 'Ajustes de asistencia guardados.',
    ],
];
