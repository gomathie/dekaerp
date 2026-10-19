<?php

return [
    'navigation' => [
        'title' => 'Categorías de gasto',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'Recibo obligatorio',
            'is-subcontracting'        => 'Coste de subcontratista o transportista',
            'is-subcontracting-helper' => 'Los costes de esta categoría se pagan a un proveedor (transportista, agente) y se facturan por proveedor.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'Recibo obligatorio',
            'is-subcontracting' => 'Subcontratación',
        ],
    ],
];
