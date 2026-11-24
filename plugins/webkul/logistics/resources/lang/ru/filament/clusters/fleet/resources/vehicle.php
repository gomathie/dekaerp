<?php

return [
    'navigation' => [
        'title' => 'Транспортные средства',
    ],

    'form' => [
        'fields' => [
            'registration-no'              => 'Регистрационный номер',
            'name'                         => 'Название',
            'company'                      => 'Компания',
            'vehicle-type'                 => 'Тип транспорта',
            'ownership'                    => 'Владение',
            'carrier'                      => 'Перевозчик',
            'default-driver'               => 'Водитель по умолчанию',
            'equipment'                    => 'Оборудование обслуживания',
            'telematics-device-ref'        => 'Ссылка на телематическое устройство',
            'telematics-agent-id'          => 'ID агента телематики',
            'telematics-agent-id-helper'   => 'Собственный идентификатор этого транспортного средства на платформе телематики — это не IMEI устройства. В PILOT он называется ID агента.',
            'capacity-kg'                  => 'Грузоподъёмность',
            'capacity-m3'                  => 'Вместимость по объёму',
            'is-active'                    => 'Активен',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Госномер',
            'name'                  => 'Название',
            'ownership'             => 'Владение',
            'vehicle-type'          => 'Тип',
            'carrier'               => 'Перевозчик',
            'capacity-kg'           => 'Вместимость',
            'telematics-device-ref' => 'Телематика',
            'telematics-agent-id'   => 'Агент телематики',
            'company'               => 'Компания',
            'is-active'             => 'Активен',
        ],

        'filters' => [
            'ownership'    => 'Владение',
            'vehicle-type' => 'Тип транспорта',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Транспортное средство',
            'capacity'     => 'Вместимость',
            'integrations' => 'Интеграции',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Импорт из оборудования обслуживания',

            'fields' => [
                'equipment' => 'Оборудование',
            ],

            'notification' => [
                'title' => 'Импорт оборудования обслуживания завершён',
                'body'  => 'Создано: :created, пропущено: :skipped.',
            ],
        ],
    ],
];
