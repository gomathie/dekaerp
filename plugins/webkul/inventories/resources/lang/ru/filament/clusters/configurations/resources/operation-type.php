<?php

return [
    'navigation' => [
        'title' => 'Operation Types',
        'group' => 'Склад Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type'             => 'Operation Тип',
                    'operator-type-placeholder' => 'eg. Receptions',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable Вкл.',
                'description' => 'Выбрать places где этот маршрут можно быть выбрано.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'operator-type'                      => 'Operation Тип',
                    'sequence-prefix'                    => 'Нумерация Префикс',
                    'generate-shipping-labels'           => 'Generate Shipping Labels',
                    'warehouse'                          => 'Склад',
                    'show-reception-report'              => 'Показать Reception Отчёт в Validation',
                    'show-reception-report-hint-tooltip' => 'Если выбрано, система автоматически display reception отчёт upon validation, provided moves до allocate.',
                    'company'                            => 'Компания',
                    'return-type'                        => 'Return Тип',
                    'create-backorder'                   => 'Создать Backorder',
                    'move-type'                          => 'Переместить Тип',
                    'move-type-hint-tooltip'             => 'Unless defined от источник документ, этот serve как по умолчанию picking policy для этот operation тип.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'  => 'Партии/Серийный номер Numbers',

                        'fields' => [
                            'create-new'                => 'Создать Новое',
                            'create-new-hint-tooltip'   => 'Если выбрано, система assume вы intend до создать новое Партии/Серийный номер Numbers, allowing вы до введите их в text поле.',
                            'use-existing'              => 'Использовать Existing',
                            'use-existing-hint-tooltip' => 'Если выбрано, вы можно выбрать Партии/Серийный номер Numbers или opt не до назначить любой. Этот allows запас до быть создано без партия или без restrictions вкл. партия используется.',
                        ],
                    ],

                    'locations' => [
                        'title'  => 'Места',

                        'fields' => [
                            'source-location'                   => 'Источник Место',
                            'source-location-hint-tooltip'      => 'Этот serves как по умолчанию источник место когда вручную creating этот operation. However, это можно быть изменено later, и маршруты май назначить different по умолчанию место.',
                            'destination-location'              => 'Назначение Место',
                            'destination-location-hint-tooltip' => 'Этот по умолчанию назначение место для вручную создано операции. However, это можно быть modified later, и маршруты май назначить different по умолчанию место.',
                        ],
                    ],

                    'packages' => [
                        'title'  => 'Упаковки',

                        'fields' => [
                            'show-entire-package'              => 'Переместить Entire Упаковка',
                            'show-entire-package-hint-tooltip' => 'Если выбрано, вы можно переместить entire упаковки.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title'  => 'Hardware',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title'  => 'Печать вкл. Validation',

                        'fields' => [
                            'delivery-slip'              => 'Доставка Slip',
                            'delivery-slip-hint-tooltip' => 'Если выбрано, система автоматически печать доставка slip когда picking validated.',

                            'return-slip'              => 'Return Slip',
                            'return-slip-hint-tooltip' => 'Если выбрано, система автоматически печать return slip когда picking validated.',

                            'product-labels'              => 'Товар Labels',
                            'product-labels-hint-tooltip' => 'Если выбрано, система автоматически печать товар labels когда picking validated.',

                            'lots-labels'              => 'Партия/SN Labels',
                            'lots-labels-hint-tooltip' => 'Если выбрано, система автоматически печать партия/серийный номер номер labels когда picking validated.',

                            'reception-report'              => 'Reception Отчёт',
                            'reception-report-hint-tooltip' => 'Если выбрано, система автоматически печать reception отчёт когда picking validated и contains назначен moves.',

                            'reception-report-labels'              => 'Reception Отчёт Labels',
                            'reception-report-labels-hint-tooltip' => 'Если выбрано, система автоматически печать reception отчёт labels когда picking validated.',

                            'package-content'              => 'Упаковка Content',
                            'package-content-hint-tooltip' => 'Если выбрано, система автоматически печать упаковка подробности и их contents когда picking validated.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title'  => 'Печать вкл. "Put в Pack"',

                        'fields' => [
                            'package-label'              => 'Упаковка Подпись',
                            'package-label-hint-tooltip' => 'Если выбрано, система автоматически печать упаковка подпись когда "Put в Pack" button используется.',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'warehouse'  => 'Склад',
            'company'    => 'Компания',
            'deleted-at' => 'Удалено',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'type'       => 'Тип',
            'warehouse'  => 'Склад',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'type'      => 'Тип',
            'warehouse' => 'Склад',
            'company'   => 'Компания',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operation Тип restored',
                    'body'  => 'operation тип есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Operation Тип удалено',
                    'body'  => 'operation тип есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operation Тип force удалено',
                        'body'  => 'operation тип есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Operation Тип could не быть удалено',
                        'body'  => 'operation тип нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operation Types restored',
                    'body'  => 'operation types есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Operation Types удалено',
                    'body'  => 'operation types есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Операции Types force удалено',
                        'body'  => 'operation types есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Операции Types could не быть удалено',
                        'body'  => 'operation types нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Создать Operation Тип',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name' => 'Название',
                ],
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'created-by'   => 'Создано От',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title' => 'Общее',

                'entries' => [
                    'type'                       => 'Operation Тип',
                    'sequence_code'              => 'Нумерация Код',
                    'print_label'                => 'Печать Подпись',
                    'warehouse'                  => 'Склад',
                    'reservation_method'         => 'Reservation Method',
                    'auto_show_reception_report' => 'Auto Показать Reception Отчёт',
                    'company'                    => 'Компания',
                    'return_operation_type'      => 'Return Operation Тип',
                    'create_backorder'           => 'Создать Backorder',
                    'move_type'                  => 'Переместить Тип',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Партии',

                        'entries' => [
                            'use_create_lots'   => 'Использовать Создать Партии',
                            'use_existing_lots' => 'Использовать Existing Партии',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Места',

                        'entries' => [
                            'source_location'      => 'Источник Место',
                            'destination_location' => 'Назначение Место',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Hardware',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Печать вкл. Validation',

                        'entries' => [
                            'auto_print_delivery_slip'           => 'Auto Печать Доставка Slip',
                            'auto_print_return_slip'             => 'Auto Печать Return Slip',
                            'auto_print_product_labels'          => 'Auto Печать Товар Labels',
                            'auto_print_lot_labels'              => 'Auto Печать Партия Labels',
                            'auto_print_reception_report'        => 'Auto Печать Reception Отчёт',
                            'auto_print_reception_report_labels' => 'Auto Печать Reception Отчёт Labels',
                            'auto_print_packages'                => 'Auto Печать Упаковки',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'Печать вкл. Pack',

                        'entries' => [
                            'auto_print_package_label' => 'Auto Печать Упаковка Подпись',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
