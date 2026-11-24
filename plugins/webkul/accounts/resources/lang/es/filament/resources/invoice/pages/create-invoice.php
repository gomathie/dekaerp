<?php

return [
    'notification' => [
        'title' => 'Factura creada',
        'body'  => 'La factura se ha creado correctamente.',
    ],

    'direct-sale' => [
        'button'           => 'Nueva venta directa',
        'walk-in-customer' => 'Cliente ocasional',
    ],

    'receipt' => [
        'label'     => 'Imprimir recibo',
        'heading'   => 'Recibo',
        'title'     => 'Recibo',
        'number'    => 'Recibo n.º',
        'date'      => 'Fecha',
        'customer'  => 'Cliente',
        'served-by' => 'Atendido por',
        'item'      => 'Artículo',
        'qty'       => 'Cant.',
        'amount'    => 'Importe',
        'subtotal'  => 'Subtotal',
        'tax'       => 'Impuesto',
        'total'     => 'Total',
        'paid'      => 'Pagado',
        'balance'   => 'Saldo pendiente',
        'thank-you' => 'Gracias por su compra.',
    ],

    'sale-currency' => [
        'heading'     => '¿En qué moneda se hizo esta venta?',
        'description' => 'Elija la moneda en la que paga el cliente. La factura se emite en esa moneda y su valor en la moneda de la empresa se muestra junto a los totales.',
        'base'        => ':code (moneda de la empresa)',
        'continue'    => 'Continuar',
        'rate'        => 'Tipo de cambio',
        'feed'        => 'Tipo del día según la fuente diaria, con fecha :date. Cámbielo si acordó otro tipo.',
        'stale'       => 'El último tipo registrado es del :date, hace más de 3 días. Revíselo antes de continuar.',
        'no-rate'     => 'No hay ningún tipo registrado para esta moneda. Escriba el tipo acordado.',
        'chatter'     => 'Tipo de cambio confirmado al crear la factura: :rate.',
        'change'      => 'Moneda y tipo',
        'kept'        => 'Tipo fijado para esta venta: :rate.',
        'kept-by'     => 'Tipo fijado para esta venta: :rate, confirmado por :name.',
        'dropped'     => 'El tipo confirmado era para otra moneda, así que esta factura usa el tipo del día.',
    ],
];
