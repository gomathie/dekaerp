<?php

return [
    'navigation' => [
        'title' => 'Viagens',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Veículo e equipe',
            'schedule' => 'Programação',
        ],

        'fields' => [
            'company'            => 'Empresa',
            'number'             => 'Número da viagem',
            'number-placeholder' => 'Atribuído automaticamente',
            'vehicle'            => 'Veículo',
            'driver'             => 'Motorista',
            'dispatcher'         => 'Responsável pelo despacho',
            'planned-start-at'   => 'Início planejado',
            'planned-end-at'     => 'Término planejado',
            'odometer-start'     => 'Odômetro inicial',
            'odometer-end'       => 'Odômetro final',
            'notes'              => 'Observações',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Viagem',
            'state'            => 'Status',
            'vehicle'          => 'Veículo',
            'driver'           => 'Motorista',
            'shipments'        => 'Remessas',
            'planned-start-at' => 'Início planejado',
            'company'          => 'Empresa',
        ],

        'filters' => [
            'state'   => 'Status',
            'vehicle' => 'Veículo',
            'driver'  => 'Motorista',
        ],
    ],

    'infolist' => [
        'summary'         => 'Viagem',
        'schedule'        => 'Programação',
        'load'            => 'Carga',
        'actual-start-at' => 'Início efetivo',
        'actual-end-at'   => 'Término efetivo',
        'capacity'        => 'Capacidade',
        'capacity-ok'     => 'Dentro da capacidade do veículo.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Remessas',
            'columns' => [
                'number'   => 'Remessa',
                'state'    => 'Status',
                'customer' => 'Cliente',
                'weight'   => 'Peso (kg)',
                'volume'   => 'Volume (m³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Adicionar remessa',
                    'field'        => 'Remessa confirmada',
                    'notification' => 'Remessa adicionada a esta viagem.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Despachar',
            'heading'      => 'Despachar esta viagem?',
            'notification' => 'Viagem despachada.',
        ],
        'startTrip' => [
            'label'        => 'Iniciar',
            'heading'      => 'Iniciar esta viagem?',
            'notification' => 'Viagem iniciada.',
        ],
        'completeTrip' => [
            'label'        => 'Concluir',
            'heading'      => 'Concluir esta viagem?',
            'notification' => 'Viagem concluída.',
        ],
    ],
];
