<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'Mark picked up',
            'notification'         => 'Shipment marked as picked up',
            'transit-label'        => 'Start transit',
            'transit-notification' => 'Shipment marked in transit',
        ],
        'deliver' => [
            'label'                         => 'Deliver shipment',
            'notification'                  => 'Shipment delivered',
            'stop-notification'             => 'Delivery stop recorded',
            'out-for-delivery-label'        => 'Mark out for delivery',
            'out-for-delivery-notification' => 'Shipment marked out for delivery',
            'fields'                        => [
                'capture-pod'    => 'Capture proof of delivery',
                'stop'           => 'Delivery stop',
                'recipient-name' => 'Recipient name',
                'received-at'    => 'Received at',
                'reference'      => 'Reference',
                'notes'          => 'Notes',
                'photo'          => 'Delivery photo',
                'signature'      => 'Recipient signature',
            ],
        ],
        'fail' => [
            'label'               => 'Record failed delivery',
            'reason'              => 'Reason',
            'notification'        => 'Failed delivery recorded',
            'resolve-label'       => 'Resolve failed delivery',
            'resolution'          => 'Next step',
            'retry-notification'  => 'Shipment ready for another delivery attempt',
            'return-notification' => 'Shipment marked as returned',
            'resolutions'         => [
                'retry'  => 'Retry delivery',
                'return' => 'Mark returned',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'Proof of delivery',
        'columns' => [
            'recipient'    => 'Recipient',
            'received-at'  => 'Received at',
            'stop'         => 'Stop',
            'captured-via' => 'Captured via',
            'photo'        => 'Photo',
            'signature'    => 'Signature',
        ],
    ],
    'validation' => [
        'pod-required'        => 'Proof of delivery is required before this shipment can be delivered.',
        'stop-required'       => 'Select the delivery stop this proof belongs to.',
        'stop-unavailable'    => 'The selected delivery stop is no longer available.',
        'upload-failed'       => 'The proof file could not be stored. Please try again.',
        'resolution-required' => 'Choose whether to retry or return the failed delivery.',
    ],
];
