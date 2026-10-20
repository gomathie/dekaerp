<?php

return [
    'navigation' => [
        'title' => 'Veículos',
    ],

    'form' => [
        'fields' => [
            'registration-no'            => 'Número de registro',
            'name'                       => 'Nome',
            'company'                    => 'Empresa',
            'vehicle-type'               => 'Tipo de veículo',
            'ownership'                  => 'Propriedade',
            'carrier'                    => 'Transportadora',
            'default-driver'             => 'Motorista padrão',
            'equipment'                  => 'Equipamento de manutenção',
            'telematics-device-ref'      => 'Referência do dispositivo telemático',
            'telematics-agent-id'        => 'ID do agente telemático',
            'telematics-agent-id-helper' => 'O identificador próprio deste veículo na plataforma de telemetria, que não é o IMEI do dispositivo. A PILOT chama isso de ID do agente.',
            'capacity-kg'                => 'Capacidade de peso',
            'capacity-m3'                => 'Capacidade de volume',
            'is-active'                  => 'Ativo',
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
            'telematics-agent-id'   => 'Agente telemático',
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
