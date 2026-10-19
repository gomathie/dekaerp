<?php

return [
    'validation' => [
        'upload-failed' => 'No se pudo guardar el archivo del recibo. Inténtelo de nuevo.',
    ],
    'relation-manager' => [
        'title' => 'Costes',

        'fields' => [
            'date'           => 'Fecha',
            'category'       => 'Categoría',
            'payee'          => 'Pagado a',
            'amount'         => 'Importe',
            'approved-total' => 'Aprobado',
            'state'          => 'Estado',
            'bill'           => 'Factura de proveedor',
            'not-billed'     => 'Sin facturar',
        ],
    ],

    'margin' => [
        'heading' => 'Ingresos y costes',
        'revenue' => 'Ingresos',
        'costs'   => 'Costes',
        'margin'  => 'Margen',
        'helper'  => 'Cargos facturables frente a costes aprobados. Los costes en borrador y rechazados no se incluyen.',
    ],

    'actions' => [
        'post-bill' => [
            'label'          => 'Crear factura de proveedor',
            'heading'        => '¿Crear facturas de proveedor en borrador?',
            'description'    => 'Cada coste aprobado y sin facturar de este envío se convierte en una factura en borrador, una por beneficiario. No se contabiliza nada: Finanzas aún debe revisarlas y contabilizarlas.',
            'cannot-post'    => 'Estos costes todavía no se pueden facturar',
            'draft-reminder' => 'Creadas como borradores. Revíselas y contabilícelas en Contabilidad.',
            'notification'   => '{0}No quedaba nada por facturar.|{1}Se creó una factura de proveedor en borrador.|[2,*]Se crearon :count facturas de proveedor en borrador.',
        ],
    ],
];
