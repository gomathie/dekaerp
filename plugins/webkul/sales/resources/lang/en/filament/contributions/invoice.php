<?php

return [
    'create-sale-order' => [
        'label'  => 'Create sales order',
        'helper' => 'When the invoice is confirmed, a confirmed sales order is made from it, with delivery for stocked products, so it shows in Sales and Inventory.',
    ],

    'linked-order' => [
        'label' => 'Sales order',
    ],
    'goods-delivered-now' => [
        'label'  => 'Customer has taken the goods',
        'helper' => 'Records the delivery as done when the invoice is confirmed. Untick to leave it for the warehouse.',
    ],

    'delivery' => [
        'label'               => 'Delivery',
        'waiting'             => 'Waiting for the warehouse',
        'done'                => 'Delivered',
        'mark-delivered'      => 'Mark as delivered',
        'confirm-heading'     => 'Record the delivery?',
        'confirm-description' => 'The items on this invoice will be recorded as delivered and taken out of stock. Items without enough stock stay waiting.',
        'delivered'           => 'Delivery recorded.',
        'not-enough-stock'    => 'Some items are not in stock; they are still waiting in Inventory.',
    ],
];
