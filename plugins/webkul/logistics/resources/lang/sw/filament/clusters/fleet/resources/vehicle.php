<?php

return [
    'navigation' => [
        'title' => 'Magari',
    ],

    'form' => [
        'fields' => [
            'registration-no'              => 'Namba ya usajili',
            'name'                         => 'Jina',
            'company'                      => 'Kampuni',
            'vehicle-type'                 => 'Aina ya gari',
            'ownership'                    => 'Umiliki',
            'carrier'                      => 'Msafirishaji',
            'default-driver'               => 'Dereva chaguo-msingi',
            'equipment'                    => 'Kifaa cha matengenezo',
            'telematics-device-ref'        => 'Marejeleo ya kifaa cha telematiki',
            'telematics-agent-id'          => 'Kitambulisho cha wakala wa telematiki',
            'telematics-agent-id-helper'   => 'Kitambulisho cha jukwaa la telematiki kwa gari hili, ambacho si IMEI ya kifaa. PILOT hukiita kitambulisho cha wakala.',
            'capacity-kg'                  => 'Uwezo wa uzito',
            'capacity-m3'                  => 'Uwezo wa ujazo',
            'is-active'                    => 'Hai',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Usajili',
            'name'                  => 'Jina',
            'ownership'             => 'Umiliki',
            'vehicle-type'          => 'Aina',
            'carrier'               => 'Msafirishaji',
            'capacity-kg'           => 'Uwezo',
            'telematics-device-ref' => 'Telematiki',
            'telematics-agent-id'   => 'Wakala wa telematiki',
            'company'               => 'Kampuni',
            'is-active'             => 'Hai',
        ],

        'filters' => [
            'ownership'    => 'Umiliki',
            'vehicle-type' => 'Aina ya gari',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Gari',
            'capacity'     => 'Uwezo',
            'integrations' => 'Miunganisho',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Ingiza kutoka vifaa vya matengenezo',

            'fields' => [
                'equipment' => 'Vifaa',
            ],

            'notification' => [
                'title' => 'Uingizaji wa vifaa vya matengenezo umekamilika',
                'body'  => 'Vimeundwa :created, vimerukwa :skipped.',
            ],
        ],
    ],
];
