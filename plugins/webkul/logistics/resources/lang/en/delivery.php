<?php

return [
    'actions' => [
        'pickup' => [
            'label'        => 'Mark picked up',
            'notification' => 'Shipment marked as picked up',
        ],
        'deliver' => [
            'label'        => 'Deliver shipment',
            'notification' => 'Shipment delivered',
            'fields'       => [
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
            'label'        => 'Record failed delivery',
            'reason'       => 'Reason',
            'notification' => 'Failed delivery recorded',
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
        'pod-required'  => 'Proof of delivery is required before this shipment can be delivered.',
        'stop-required' => 'Select the delivery stop this proof belongs to.',
    ],
];
