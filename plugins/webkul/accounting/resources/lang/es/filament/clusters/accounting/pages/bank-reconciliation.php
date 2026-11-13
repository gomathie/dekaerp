<?php

return [
    'navigation' => [
        'title' => 'Conciliación bancaria',
    ],

    'import' => [
        'label'       => 'Importar extracto',
        'description' => 'Suba el archivo CSV de su banco. Indique el número de cada columna, contando desde 1 a la izquierda.',
        'journal'     => 'Cuenta bancaria',
        'file'        => 'Archivo del extracto (CSV)',
        'name'        => 'Nombre del extracto',
        'date-format' => 'Formato de fecha',
        'has-header'  => 'La primera línea es un encabezado',
        'amount-help' => 'O déjelo vacío e indique las columnas de entradas y salidas.',
        'done'        => ':count líneas importadas.',

        'columns' => [
            'date'      => 'Columna de fecha',
            'label'     => 'Columna de descripción',
            'amount'    => 'Columna de importe',
            'money_in'  => 'Columna de entradas',
            'money_out' => 'Columna de salidas',
            'partner'   => 'Columna de cliente o proveedor',
            'reference' => 'Columna de referencia',
        ],
    ],

    'columns' => [
        'date'    => 'Fecha',
        'label'   => 'Descripción',
        'partner' => 'Cliente o proveedor',
        'journal' => 'Cuenta bancaria',
        'amount'  => 'Importe',
        'status'  => 'Estado',
        'payment' => 'Pago',
    ],

    'status' => [
        'reconciled'   => 'Conciliada',
        'to-reconcile' => 'Por conciliar',
    ],

    'match' => [
        'label'       => 'Conciliar pago',
        'description' => 'Pagos de esta cuenta bancaria por el mismo importe que aún esperan al banco.',
        'payment'     => 'Pago',
        'none'        => 'Ningún pago coincide. Registre primero el pago en la factura, o contabilice la línea en una cuenta.',
        'done'        => 'Línea conciliada. El pago ya está pagado.',
    ],

    'book' => [
        'label'       => 'Contabilizar en una cuenta',
        'description' => 'Para comisiones bancarias, intereses o transferencias: registra la línea en la cuenta elegida.',
        'account'     => 'Cuenta',
        'entry-label' => 'Etiqueta',
        'done'        => 'Línea contabilizada.',
    ],

    'empty' => [
        'heading'     => 'Nada por conciliar',
        'description' => 'Importe un extracto de su banco para empezar.',
    ],
];
