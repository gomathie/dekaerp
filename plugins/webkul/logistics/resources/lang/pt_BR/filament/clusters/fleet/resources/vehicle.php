<?php

return [
    'navigation' => [
        'title' => 'Veículos',
    ],

    'form' => [
        'fields' => [
            'registration-no'       => 'Número de registro',
            'name'                  => 'Nome',
            'company'               => 'Empresa',
            'vehicle-type'          => 'Tipo de veículo',
            'ownership'             => 'Propriedade',
            'carrier'               => 'Transportadora',
            'default-driver'        => 'Motorista padrão',
            'equipment'             => 'Equipamento de manutenção',
            'telematics-device-ref' => 'Referência do dispositivo telemático',
            'capacity-kg'           => 'Capacidade de peso',
            'capacity-m3'           => 'Capacidade de volume',
            'is-active'             => 'Ativo',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Registro',
            'name'                  => 'Nome',
            'ownership'             => 'Propriedade',
            'vehicle-type'          => 'Tipo',
            'carrier'               => 'Transportadora',
            'capacity-kg'           => 'Capacidade',
            'telematics-device-ref' => 'Telemática',
            'company'               => 'Empresa',
            'is-active'             => 'Ativo',
        ],

        'filters' => [
            'ownership'    => 'Propriedade',
            'vehicle-type' => 'Tipo de veículo',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Veículo',
            'capacity'     => 'Capacidade',
            'integrations' => 'Integrações',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Importar dos equipamentos de Manutenção',

            'fields' => [
                'equipment' => 'Equipamento',
            ],

            'notification' => [
                'title' => 'Importação de equipamentos de manutenção concluída',
                'body'  => ':created criados, :skipped ignorados.',
            ],
        ],
    ],
];
