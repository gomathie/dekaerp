<?php

return [
    'navigation' => [
        'title' => 'Conciliación bancaria',
    ],

    'import' => [
        'label'                => 'Importar extracto',
        'description'          => 'Suba el archivo CSV de su banco. Indique el número de cada columna, contando desde 1 a la izquierda. Cada cuenta bancaria recuerda sus columnas para la próxima vez; si el banco mueve una columna, se vuelve a encontrar por su encabezado.',
        'journal'              => 'Cuenta bancaria',
        'file'                 => 'Archivo del extracto (CSV)',
        'name'                 => 'Nombre del extracto',
        'date-format'          => 'Formato de fecha',
        'has-header'           => 'La primera línea es un encabezado',
        'amount-help'          => 'O déjelo vacío e indique las columnas de entradas y salidas.',
        'balance-help'         => 'Opcional: el saldo acumulado. Su valor del último día es el saldo final.',
        'closing-balance'      => 'Saldo final del extracto',
        'closing-balance-help' => 'Opcional. Sin él ni una columna de saldo, el saldo final es el del extracto anterior más estas líneas.',
        'done'                 => ':count líneas importadas.',

        'columns' => [
            'date'      => 'Columna de fecha',
            'label'     => 'Columna de descripción',
            'amount'    => 'Columna de importe',
            'money_in'  => 'Columna de entradas',
            'money_out' => 'Columna de salidas',
            'partner'   => 'Columna de cliente o proveedor',
            'reference' => 'Columna de referencia',
            'balance'   => 'Columna de saldo',
        ],
    ],

    'match-all' => [
        'label'       => 'Conciliar todas las sugerencias (:count)',
        'heading'     => '¿Conciliar todas las líneas sugeridas?',
        'description' => '{0} Ninguna línea tiene ahora un único pago sugerido.|{1} :count línea tiene un único pago sugerido del mismo importe. Se conciliará con él. Las demás líneas quedan como están.|[2,*] :count líneas tienen un único pago sugerido del mismo importe. Cada una se conciliará con su pago. Las demás líneas quedan como están.',
        'submit'      => 'Conciliar',
        'done'        => '{0} No se concilió ninguna línea.|{1} :count línea conciliada.|[2,*] :count líneas conciliadas.',
        'left'        => '{1} :count línea quedó abierta: su periodo está cerrado o cambió mientras tanto.|[2,*] :count líneas quedaron abiertas: su periodo está cerrado o cambiaron mientras tanto.',
    ],

    'balances' => [
        'heading'      => 'Control de saldo por cuenta',
        'statement'    => 'Saldo del extracto',
        'book'         => 'Saldo contable',
        'difference'   => 'Diferencia',
        'up-to'        => 'Extracto hasta el :date',
        'no-statement' => 'Aún no se ha importado ningún extracto',
        'explain'      => 'Saldo del extracto: el saldo final del último extracto importado. Saldo contable: lo que el libro mayor tiene en la cuenta; un pago cuenta cuando se concilia con una línea del extracto.',

        'state' => [
            'clean'      => 'Al día',
            'to-match'   => '{1} :count por conciliar|[2,*] :count por conciliar',
            'difference' => 'Diferencia por revisar',
            'none'       => 'Sin extracto',
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

    'match-expense' => [
        'label'       => 'Conciliar gasto',
        'description' => 'Gastos pagados desde esta cuenta bancaria por el mismo importe, a pocos días de esta línea.',
        'expense'     => 'Gasto',
        'none'        => 'Ningún gasto coincide. Registre primero el gasto o asigne la línea a una cuenta.',
        'done'        => 'Línea conciliada con el gasto. No se contabilizó nada de nuevo.',
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
