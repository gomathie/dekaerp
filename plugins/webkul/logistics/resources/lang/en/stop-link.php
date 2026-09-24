<?php

return [
    'title'    => 'Confirm delivery',
    'shipment' => 'Shipment',
    'stop'     => 'Stop',

    'form' => [
        'recipient-name'      => 'Received by',
        'recipient-name-help' => 'The name of the person who took delivery.',
        'photo'               => 'Photo',
        'photo-help'          => 'A photo of the delivered goods, the door, or the signed waybill.',
        'signature'           => 'Signature',
        'signature-help'      => 'Ask the recipient to sign in the box.',
        'signature-clear'     => 'Clear',
        'notes'               => 'Notes',
        'location'            => 'Attach my location',
        'location-attached'   => 'Location attached.',
        'location-failed'     => 'Location unavailable. You can still submit.',
        'submit'              => 'Confirm delivery',
        'submitting'          => 'Confirming…',
    ],

    'done' => [
        'title'   => 'Delivery confirmed',
        'message' => 'Thank you. The delivery has been recorded and this link is now closed.',
    ],

    'expired' => [
        'title'   => 'This link is no longer valid',
        'message' => 'It may have expired, already been used, or been withdrawn. Ask the office for a new link.',
    ],

    'actions' => [
        'send-pod-link' => [
            'label'        => 'POD link',
            'heading'      => 'One-time proof of delivery link',
            'description'  => 'Share this link with the driver. It works once, for this stop only, and expires after :hours hours. Issuing a new link cancels any earlier one.',
            'copy'         => 'Copy link',
            'no-stop'      => 'This shipment has no delivery stop still open, so there is nothing to capture.',
            'notification' => 'Link issued.',
        ],
    ],
];
