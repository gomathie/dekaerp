<?php

return [
    'title' => 'Gasto',

    'navigation' => [
        'title' => 'Gastos',
    ],

    'form' => [
        'fields' => [
            'number'              => 'Número',
            'date'                => 'Fecha',
            'amount'              => 'Importe',
            'currency'            => 'Moneda',
            'expense-account'     => 'Cuenta de gastos',
            'paid-from'           => 'Pagado desde',
            'vendor'              => 'Proveedor',
            'vendor-name'         => 'Nombre del proveedor',
            'tax'                 => 'Impuesto',
            'tax-amount'          => 'Importe del impuesto',
            'amount-includes-tax' => 'El importe incluye el impuesto',
            'reference'           => 'Referencia',
            'note'                => 'Nota',
            'receipt'             => 'Comprobante',
            'entry'               => 'Asiento contable',
            'reason'              => 'Motivo del cambio',
        ],

        'hints' => [
            'currency'            => 'Otra moneda usa el tipo de cambio del día, como en las facturas.',
            'expense-account'     => 'Solo las cuentas de gastos de esta empresa. La última usada se propone primero.',
            'paid-from'           => 'Todas las cuentas bancarias y de caja de la empresa.',
            'vendor'              => 'Opcional. Escriba un nombre nuevo para añadir el proveedor.',
            'tax'                 => 'Opcional. En blanco: sin línea de impuesto.',
            'no-tax'              => 'Sin impuesto',
            'amount-includes-tax' => 'Activado: el impuesto se descuenta del importe. Desactivado: el impuesto se suma.',
            'receipt'             => 'Una foto o un PDF, 10 MB como máximo. En un teléfono puede hacer una foto.',
            'reason'              => 'Este gasto ya está en la contabilidad. Indique por qué cambia.',
        ],

        'tax-included' => ':tax (incluido)',

        'summary' => [
            'empty' => 'Introduzca un importe para ver lo que se contabilizará.',
            'posts' => 'Al guardar, salen :total de la cuenta de pago: :net a la cuenta de gastos y :tax de impuesto.',
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Fecha',
            'expense-account' => 'Cuenta de gastos',
            'paid-from'       => 'Pagado desde',
            'vendor'          => 'Proveedor',
            'reference'       => 'Referencia',
            'amount'          => 'Importe',
            'receipt'         => 'Comprobante',
        ],

        'total'  => 'Total',
        'export' => 'Exportar',

        'filters' => [
            'period'          => 'Período',
            'from'            => 'Desde',
            'until'           => 'Hasta',
            'paid-from'       => 'Pagado desde',
            'expense-account' => 'Cuenta de gastos',
            'vendor'          => 'Proveedor',
            'with-receipt'    => 'Con comprobante',
        ],
    ],

    'actions' => [
        'new'          => 'Nuevo gasto',
        'save'         => 'Guardar',
        'save-and-new' => 'Guardar y nuevo',

        'delete' => [
            'label'       => 'Eliminar',
            'description' => 'El asiento del gasto se cancela, de modo que en la contabilidad el dinero vuelve a la cuenta de pago.',
            'done'        => 'Gasto eliminado',
        ],
    ],

    'notifications' => [
        'created' => 'Gasto guardado y contabilizado',
        'saved'   => 'Gasto actualizado',
        'refused' => 'El gasto no se guardó',
        'failed'  => 'Algo salió mal. No se cambió nada.',
    ],

    'export' => [
        'columns' => [
            'number'          => 'Número',
            'date'            => 'Fecha',
            'expense-account' => 'Cuenta de gastos',
            'paid-from'       => 'Pagado desde',
            'vendor'          => 'Proveedor',
            'reference'       => 'Referencia',
            'currency'        => 'Moneda',
            'amount'          => 'Importe',
            'tax'             => 'Impuesto',
            'amount-company'  => 'Importe en la moneda de la empresa',
            'note'            => 'Nota',
        ],

        'notification' => [
            'completed' => 'La exportación de gastos ha terminado: :count filas exportadas.',
            'failed'    => ':count filas no se pudieron exportar.',
        ],
    ],
];
