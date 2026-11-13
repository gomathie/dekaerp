<?php

return [
    'statement-name' => 'Extracto del :from al :to',
    'entry-label'    => 'Línea de extracto bancario',

    'fields' => [
        'date'   => 'fecha',
        'amount' => 'importe',
    ],

    'errors' => [
        'not-allowed'            => 'No puede conciliar esta cuenta bancaria.',
        'not-a-bank-journal'     => 'Elija un diario de banco o de caja.',
        'foreign-currency'       => 'Este diario usa una moneda extranjera. Por ahora solo se pueden conciliar cuentas en la moneda de la empresa.',
        'missing-bank-account'   => 'Este diario no tiene cuenta bancaria. Defina primero su cuenta por defecto.',
        'empty-file'             => 'El archivo no tiene líneas de extracto.',
        'too-many-rows'          => 'El archivo tiene más de :max líneas. Divídalo en extractos más pequeños.',
        'bad-row'                => 'Línea :row: no se puede leer el campo :field. Revise los números de columna y el formato de fecha.',
        'already-reconciled'     => 'Esta línea ya está conciliada.',
        'payment-does-not-match' => 'Ese pago no corresponde a esta línea: debe estar en el mismo diario bancario, por el mismo importe y sentido, y no estar ya conciliado.',
        'account-not-allowed'    => 'Esa cuenta no se puede usar aquí. Las cuentas de clientes y proveedores se liquidan conciliando un pago.',
    ],
];
