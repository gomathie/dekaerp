<?php

return [
    'navigation' => [
        'title' => 'Envíos',
    ],

    'global-search' => [
        'customer'  => 'Cliente',
        'state'     => 'Estado',
        'reference' => 'Referencia del cliente',
    ],

    'form' => [
        'tabs' => [
            'general'    => 'General',
            'route'      => 'Ruta y paradas',
            'cargo'      => 'Carga',
            'assignment' => 'Asignación',
        ],

        'fields' => [
            'number'                 => 'Número de envío',
            'number-placeholder'     => 'Se asigna al guardar el envío',
            'company'                => 'Empresa',
            'customer'               => 'Cliente',
            'customer-reference'     => 'Referencia del cliente',
            'sale-order'             => 'Desde pedido de venta',
            'sale-order-helper'      => 'Copia la moneda y la referencia del cliente desde un pedido de venta de este cliente.',
            'service-type'           => 'Tipo de servicio',
            'transport-mode'         => 'Modo de transporte',
            'priority'               => 'Prioridad',
            'currency'               => 'Moneda',
            'origin'                 => 'Origen',
            'destination'            => 'Destino',
            'pickup-address'         => 'Dirección de recogida',
            'delivery-address'       => 'Dirección de entrega',
            'planned-pickup-at'      => 'Recogida planificada',
            'expected-delivery-at'   => 'Entrega prevista',
            'stops'                  => 'Paradas',
            'stop-type'              => 'Tipo',
            'stop-address'           => 'Dirección',
            'stop-contact'           => 'Contacto',
            'stop-phone'             => 'Teléfono',
            'stop-planned-arrival'   => 'Llegada planificada',
            'stop-instructions'      => 'Instrucciones',
            'declared-value'         => 'Valor declarado',
            'is-fragile'             => 'Frágil',
            'is-hazardous'           => 'Peligroso',
            'is-hazardous-helper'    => 'Solo es un indicador. Aquí no se gestiona la documentación de mercancías peligrosas.',
            'cargo-lines'            => 'Carga',
            'line-description'       => 'Descripción',
            'line-package-type'      => 'Tipo de bulto',
            'line-quantity'          => 'Cantidad',
            'line-weight'            => 'Peso',
            'line-volume'            => 'Volumen',
            'instructions'           => 'Instrucciones especiales',
            'dispatcher'             => 'Responsable de despacho',
            'carrier'                => 'Transportista',
            'carrier-helper'         => 'Para transporte subcontratado. Los costes del transportista se registran como gastos.',
            'carrier-reference'      => 'Referencia del transportista',
            'waybill-no'             => 'Número de carta de porte',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Número',
            'customer'             => 'Cliente',
            'state'                => 'Estado',
            'planned-pickup-at'    => 'Recogida planificada',
            'expected-delivery-at' => 'Entrega prevista',
            'service-type'         => 'Servicio',
            'packages'             => 'Bultos',
            'dispatcher'           => 'Responsable de despacho',
            'company'              => 'Empresa',
        ],

        'filters' => [
            'state'         => 'Estado',
            'customer'      => 'Cliente',
            'service-type'  => 'Tipo de servicio',
            'pickup-from'   => 'Recogida desde',
            'pickup-until'  => 'Recogida hasta',
        ],
    ],

    'infolist' => [
        'summary'            => 'Envío',
        'route'              => 'Ruta',
        'cargo'              => 'Carga',
        'assignment'         => 'Asignación',
        'actual-pickup-at'   => 'Recogida real',
        'actual-delivery-at' => 'Entrega real',
        'total-weight'       => 'Peso total',
        'total-volume'       => 'Volumen total',
    ],

    'timeline' => [
        'title'   => 'Cronología',
        'columns' => [
            'occurred-at' => 'Cuándo',
            'type'        => 'Evento',
            'source'      => 'Origen',
            'user'        => 'Por',
            'location'    => 'Ubicación',
            'notes'       => 'Notas',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Confirmar',
            'heading'      => '¿Confirmar este envío?',
            'notification' => 'Envío confirmado',
        ],

        'hold' => [
            'label'        => 'Poner en espera',
            'heading'      => '¿Poner este envío en espera?',
            'reason'       => 'Motivo',
            'notification' => 'Envío puesto en espera',
        ],

        'release' => [
            'label'        => 'Liberar',
            'heading'      => '¿Liberar este envío?',
            'description'  => 'El envío vuelve al estado Confirmado y puede asignarse de nuevo a un viaje.',
            'notification' => 'Envío liberado',
        ],

        'cancel' => [
            'label'        => 'Cancelar envío',
            'heading'      => '¿Cancelar este envío?',
            'description'  => 'Un envío cancelado no se puede reabrir. Sus cargos se conservan como referencia.',
            'reason'       => 'Motivo',
            'notification' => 'Envío cancelado',
        ],
    ],
];
