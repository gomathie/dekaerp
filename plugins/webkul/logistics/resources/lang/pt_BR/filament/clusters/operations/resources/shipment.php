<?php

return [
    'navigation' => [
        'title' => 'Remessas',
    ],

    'global-search' => [
        'customer'  => 'Cliente',
        'state'     => 'Status',
        'reference' => 'Referência do cliente',
    ],

    'form' => [
        'tabs' => [
            'general'    => 'Geral',
            'route'      => 'Rota e paradas',
            'cargo'      => 'Carga',
            'assignment' => 'Atribuição',
        ],

        'fields' => [
            'number'                 => 'Número da remessa',
            'number-placeholder'     => 'Atribuído quando a remessa é salva',
            'company'                => 'Empresa',
            'customer'               => 'Cliente',
            'customer-reference'     => 'Referência do cliente',
            'sale-order'             => 'Do pedido de venda',
            'sale-order-helper'      => 'Copia a moeda e a referência do cliente de um pedido de venda deste cliente.',
            'service-type'           => 'Tipo de serviço',
            'transport-mode'         => 'Modal de transporte',
            'priority'               => 'Prioridade',
            'currency'               => 'Moeda',
            'origin'                 => 'Origem',
            'destination'            => 'Destino',
            'pickup-address'         => 'Endereço de coleta',
            'delivery-address'       => 'Endereço de entrega',
            'planned-pickup-at'      => 'Coleta planejada',
            'expected-delivery-at'   => 'Entrega prevista',
            'stops'                  => 'Paradas',
            'stop-type'              => 'Tipo',
            'stop-address'           => 'Endereço',
            'stop-contact'           => 'Contato',
            'stop-phone'             => 'Telefone',
            'stop-planned-arrival'   => 'Chegada planejada',
            'stop-instructions'      => 'Instruções',
            'declared-value'         => 'Valor declarado',
            'is-fragile'             => 'Frágil',
            'is-hazardous'           => 'Perigosa',
            'is-hazardous-helper'    => 'Somente sinalização. A documentação de produtos perigosos não é tratada aqui.',
            'cargo-lines'            => 'Carga',
            'line-description'       => 'Descrição',
            'line-package-type'      => 'Tipo de embalagem',
            'line-quantity'          => 'Quantidade',
            'line-weight'            => 'Peso',
            'line-volume'            => 'Volume',
            'instructions'           => 'Instruções especiais',
            'dispatcher'             => 'Responsável pelo despacho',
            'carrier'                => 'Transportadora',
            'carrier-helper'         => 'Para transporte subcontratado. Os custos da transportadora são registrados como despesas.',
            'carrier-reference'      => 'Referência da transportadora',
            'waybill-no'             => 'Número do conhecimento de transporte',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Número',
            'customer'             => 'Cliente',
            'state'                => 'Status',
            'planned-pickup-at'    => 'Coleta planejada',
            'expected-delivery-at' => 'Entrega prevista',
            'service-type'         => 'Serviço',
            'packages'             => 'Embalagens',
            'dispatcher'           => 'Responsável pelo despacho',
            'company'              => 'Empresa',
        ],

        'filters' => [
            'state'         => 'Status',
            'customer'      => 'Cliente',
            'service-type'  => 'Tipo de serviço',
            'pickup-from'   => 'Coleta a partir de',
            'pickup-until'  => 'Coleta até',
        ],
    ],

    'infolist' => [
        'summary'            => 'Remessa',
        'route'              => 'Rota',
        'cargo'              => 'Carga',
        'assignment'         => 'Atribuição',
        'actual-pickup-at'   => 'Coleta efetiva',
        'actual-delivery-at' => 'Entrega efetiva',
        'total-weight'       => 'Peso total',
        'total-volume'       => 'Volume total',
    ],

    'timeline' => [
        'title'   => 'Linha do tempo',
        'columns' => [
            'occurred-at' => 'Quando',
            'type'        => 'Evento',
            'source'      => 'Origem',
            'user'        => 'Por',
            'location'    => 'Localização',
            'notes'       => 'Observações',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Confirmar',
            'heading'      => 'Confirmar esta remessa?',
            'notification' => 'Remessa confirmada',
        ],

        'hold' => [
            'label'        => 'Colocar em espera',
            'heading'      => 'Colocar esta remessa em espera?',
            'reason'       => 'Motivo',
            'notification' => 'Remessa colocada em espera',
        ],

        'release' => [
            'label'        => 'Liberar',
            'heading'      => 'Liberar esta remessa?',
            'description'  => 'A remessa volta para Confirmado e pode ser atribuída a uma viagem novamente.',
            'notification' => 'Remessa liberada',
        ],

        'cancel' => [
            'label'        => 'Cancelar remessa',
            'heading'      => 'Cancelar esta remessa?',
            'description'  => 'Uma remessa cancelada não pode ser reaberta. Suas cobranças permanecem para referência.',
            'reason'       => 'Motivo',
            'notification' => 'Remessa cancelada',
        ],
    ],
];
