<?php

return [
    'navigation' => [
        'title' => 'Configuración',
    ],

    'title'      => 'Configuración de Logística',
    'subheading' => 'Configuración para :company. Logística se activa y desactiva por empresa.',
    'no-company' => 'Seleccione primero una empresa: la configuración de Logística se guarda por empresa.',

    'sections' => [
        'status'     => 'Estado',
        'delivery'   => 'Comprobante de entrega',
        'operations' => 'Operaciones',
        'accounting' => 'Contabilidad',
    ],

    'status' => [
        'enabled'  => 'Logística está activada para esta empresa',
        'disabled' => 'Logística no está activada para esta empresa',
    ],

    'readiness' => [
        'label' => 'Preparación',
        'ok'    => 'Todo lo que necesita Logística está disponible.',
    ],

    'fields' => [
        'require-pod-for-delivery'    => 'Exigir comprobante de entrega para marcar un envío como entregado',
        'require-pod-photo'           => 'Exigir una foto con el comprobante de entrega',
        'stop-link-ttl-hours'         => 'Validez del enlace de parada (horas)',
        'default-service-type'        => 'Tipo de servicio predeterminado',
        'capacity-check'              => 'Comprobación de capacidad del vehículo',
        'overdue-grace-minutes'       => 'Atrasado después de (minutos posteriores a la hora prevista)',
        'free-waiting-minutes'        => 'Tiempo de espera gratuito en una parada (minutos)',
        'free-waiting-minutes-helper' => 'Después de este tiempo se sugiere un cargo por espera. Nunca se añade automáticamente.',
        'invoice-journal'             => 'Diario para facturas de cliente',
        'bill-journal'                => 'Diario para facturas de proveedor',
        'default-expense-account'     => 'Cuenta de gastos predeterminada',
        'expense-approval-required'   => 'Los gastos necesitan aprobación antes de facturarse',
    ],

    'actions' => [
        'save' => 'Guardar',

        'enable' => [
            'label'                     => 'Activar Logística',
            'heading'                   => '¿Activar Logística para esta empresa?',
            'description'               => 'Los menús y registros de Logística estarán disponibles para los usuarios de esta empresa que tengan permisos de Logística.',
            'description-with-problems' => 'Logística puede activarse, pero algunas funciones no estarán disponibles hasta que se corrija lo siguiente: :problems',
            'notification'              => 'Logística activada',
        ],

        'disable' => [
            'label'        => 'Desactivar Logística',
            'heading'      => '¿Desactivar Logística para esta empresa?',
            'description'  => 'Logística se oculta y no se pueden crear nuevos registros para esta empresa. Los datos existentes se conservan y, al activarla de nuevo, se restaura el acceso.',
            'notification' => 'Logística desactivada',
        ],
    ],

    'notifications' => [
        'saved' => 'Configuración guardada',
    ],
];
