<?php

return [
    'title'      => 'Factura recurrente',
    'navigation' => 'Facturas recurrentes',

    'sections' => [
        'details' => 'Programación',
        'lines'   => 'Líneas de factura',
        'history' => 'Historial de facturas',
    ],

    'fields' => [
        'name'         => 'Nombre de la plantilla',
        'customer'     => 'Cliente',
        'journal'      => 'Diario de ventas',
        'currency'     => 'Moneda',
        'responsible'  => 'Responsable',
        'payment-term' => 'Condiciones de pago',
        'next-date'    => 'Fecha de la próxima factura',
        'frequency'    => 'Repetir',
        'ends-on'      => 'Fecha de fin',
        'auto-email'   => 'Confirmar y enviar por correo automáticamente',
        'notes'        => 'Notas',
        'product'      => 'Producto o servicio',
        'description'  => 'Descripción',
        'quantity'     => 'Cantidad',
        'price'        => 'Precio unitario',
        'discount'     => 'Descuento (%)',
        'taxes'        => 'Impuestos',
        'status'       => 'Estado',
        'last-issue'   => 'Último problema',
        'invoice'      => 'Factura',
        'period-start' => 'Inicio del periodo',
        'period-end'   => 'Fin del periodo',
    ],

    'frequencies' => [
        'weekly'    => 'Semanal',
        'monthly'   => 'Mensual',
        'quarterly' => 'Trimestral',
        'yearly'    => 'Anual',
    ],

    'actions' => [
        'create'       => 'Nueva factura recurrente',
        'add-line'     => 'Añadir línea',
        'generate-now' => 'Crear la próxima factura ahora',
        'pause'        => 'Pausar',
        'resume'       => 'Reanudar',
        'stop'         => 'Detener',
        'history'      => 'Historial',
    ],

    'errors' => [
        'inactive-customer' => 'Este cliente está inactivo. Actualice el cliente antes de crear una factura.',
        'customer-email'    => 'Añada un correo electrónico a este cliente antes de activar el envío automático.',
        'journal'           => 'Elija un diario de ventas activo de esta empresa.',
        'currency'          => 'Elija una moneda disponible.',
        'lines'             => 'Añada al menos una línea de factura.',
        'locked-period'     => 'La fecha de la próxima factura está en un periodo contable bloqueado.',
        'frequency'         => 'Elija una frecuencia admitida.',
        'company-mismatch'  => 'Este valor debe pertenecer a la empresa de la plantilla.',
        'responsible'       => 'Elija un usuario de esta empresa.',
        'state'             => 'Esta factura recurrente no puede pasar a ese estado.',
        'end-date'          => 'La fecha de fin debe ser igual o posterior a la fecha de la próxima factura.',
        'email-failed'      => 'Se creó la factura :number, pero no se pudo enviar el correo. Abra la factura y envíela de nuevo.',
    ],

    'notifications' => [
        'created' => [
            'title' => 'Una factura recurrente está lista para revisar',
            'body'  => 'Se creó el borrador de factura :number. Revíselo y envíelo al cliente.',
            'view'  => 'Abrir factura',
        ],
    ],
];
