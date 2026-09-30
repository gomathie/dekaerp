<?php

return [
    'not-enabled'       => 'Logística no está activada para la empresa n.º :company. Un administrador puede activarla en Logística > Configuración > Configuración.',
    'company-mismatch'  => 'Este :record pertenece a una empresa distinta de su :related.',
    'uninstall-blocked' => 'No se puede desinstalar Logística mientras existan :count envío(s): al desinstalarla se eliminan todos los datos de logística. Haga una copia de seguridad de la base de datos y después establezca LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true para continuar.',

    'invalid-transition'      => 'Un envío no puede pasar de “:from” a “:to”.',
    'invalid-trip-transition' => 'Un viaje no puede pasar de “:from” a “:to”.',

    'trip-no-vehicle'              => 'Asigne un vehículo a este viaje antes de despacharlo.',
    'trip-no-driver'               => 'Asigne un conductor a este viaje antes de despacharlo.',
    'trip-inactive-vehicle'        => 'El vehículo :vehicle está archivado y no se puede despachar.',
    'trip-inactive-driver'         => 'El conductor :driver está archivado y no se puede despachar.',
    'trip-expired-license'         => 'El permiso del conductor :driver caducó el :date.',
    'trip-shipment-not-confirmed'  => 'El envío :shipment debe estar confirmado antes de poder añadirse a un viaje.',

    'nothing-to-invoice' => 'El envío :shipment no tiene cargos facturables pendientes.',

    'receipt-required' => 'Los gastos de la categoría “:category” necesitan un recibo adjunto antes de poder enviarse o aprobarse.',

    'expense-not-attributable' => 'Un gasto debe estar vinculado a un envío, un viaje o un vehículo antes de poder enviarse o aprobarse.',

    'nothing-to-bill'           => 'El envío :shipment no tiene gastos aprobados pendientes de facturar.',
    'missing-partner'           => 'El gasto “:expense” no tiene beneficiario, por lo que no hay nadie a quien emitir la factura.',
    'missing-employee-contact'  => ':employee no tiene un registro de contacto, por lo que no se le puede facturar un reembolso. Añada primero un contacto al empleado.',
    'missing-bill-journal'      => 'Esta empresa no tiene configurado un diario de facturas de proveedor. Elija uno en Logística > Configuración > Configuración.',
    'missing-expense-account'   => 'Esta empresa no tiene configurada una cuenta de gastos predeterminada. Elija una en Logística > Configuración > Configuración.',
];
