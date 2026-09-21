<?php

return [
    'navigation' => [
        'title' => 'Shipments',
    ],

    'global-search' => [
        'customer'  => 'Customer',
        'state'     => 'Status',
        'reference' => 'Customer reference',
    ],

    'form' => [
        'tabs' => [
            'general'    => 'General',
            'route'      => 'Route & stops',
            'cargo'      => 'Cargo',
            'assignment' => 'Assignment',
        ],

        'fields' => [
            'number'                 => 'Shipment number',
            'number-placeholder'     => 'Assigned when the shipment is saved',
            'company'                => 'Company',
            'customer'               => 'Customer',
            'customer-reference'     => 'Customer reference',
            'sale-order'             => 'From sales order',
            'sale-order-helper'      => 'Copies the currency and customer reference from a sales order of this customer.',
            'service-type'           => 'Service type',
            'transport-mode'         => 'Transport mode',
            'priority'               => 'Priority',
            'currency'               => 'Currency',
            'origin'                 => 'Origin',
            'destination'            => 'Destination',
            'pickup-address'         => 'Pickup address',
            'delivery-address'       => 'Delivery address',
            'planned-pickup-at'      => 'Planned pickup',
            'expected-delivery-at'   => 'Expected delivery',
            'stops'                  => 'Stops',
            'stop-type'              => 'Type',
            'stop-address'           => 'Address',
            'stop-contact'           => 'Contact',
            'stop-phone'             => 'Phone',
            'stop-planned-arrival'   => 'Planned arrival',
            'stop-instructions'      => 'Instructions',
            'declared-value'         => 'Declared value',
            'is-fragile'             => 'Fragile',
            'is-hazardous'           => 'Hazardous',
            'is-hazardous-helper'    => 'Flag only. Dangerous-goods paperwork is not handled here.',
            'cargo-lines'            => 'Cargo',
            'line-description'       => 'Description',
            'line-package-type'      => 'Package type',
            'line-quantity'          => 'Quantity',
            'line-weight'            => 'Weight',
            'line-volume'            => 'Volume',
            'instructions'           => 'Special instructions',
            'dispatcher'             => 'Dispatcher',
            'carrier'                => 'Carrier',
            'carrier-helper'         => 'For subcontracted transport. Carrier costs are recorded as expenses.',
            'carrier-reference'      => 'Carrier reference',
            'waybill-no'             => 'Waybill number',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Number',
            'customer'             => 'Customer',
            'state'                => 'Status',
            'planned-pickup-at'    => 'Planned pickup',
            'expected-delivery-at' => 'Expected delivery',
            'service-type'         => 'Service',
            'packages'             => 'Packages',
            'dispatcher'           => 'Dispatcher',
            'company'              => 'Company',
        ],

        'filters' => [
            'state'         => 'Status',
            'customer'      => 'Customer',
            'service-type'  => 'Service type',
            'pickup-from'   => 'Pickup from',
            'pickup-until'  => 'Pickup until',
        ],
    ],

    'infolist' => [
        'summary'            => 'Shipment',
        'route'              => 'Route',
        'cargo'              => 'Cargo',
        'assignment'         => 'Assignment',
        'actual-pickup-at'   => 'Actual pickup',
        'actual-delivery-at' => 'Actual delivery',
        'total-weight'       => 'Total weight',
        'total-volume'       => 'Total volume',
    ],

    'timeline' => [
        'title'   => 'Timeline',
        'columns' => [
            'occurred-at' => 'When',
            'type'        => 'Event',
            'source'      => 'Source',
            'user'        => 'By',
            'location'    => 'Location',
            'notes'       => 'Notes',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Confirm',
            'heading'      => 'Confirm this shipment?',
            'notification' => 'Shipment confirmed',
        ],

        'hold' => [
            'label'        => 'Put on hold',
            'heading'      => 'Put this shipment on hold?',
            'reason'       => 'Reason',
            'notification' => 'Shipment put on hold',
        ],

        'release' => [
            'label'        => 'Release',
            'heading'      => 'Release this shipment?',
            'description'  => 'The shipment goes back to Confirmed and can be assigned to a trip again.',
            'notification' => 'Shipment released',
        ],

        'cancel' => [
            'label'        => 'Cancel shipment',
            'heading'      => 'Cancel this shipment?',
            'description'  => 'A cancelled shipment cannot be reopened. Its charges stay for reference.',
            'reason'       => 'Reason',
            'notification' => 'Shipment cancelled',
        ],
    ],
];
