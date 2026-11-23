<?php

return [
    'title' => 'Manage Shughuli',

    'form' => [
        'enable-work-orders' => [
            'label'       => 'Agizo la kazis',
            'helper-text' => 'Execute shughuli saa designated work centers.',
            'link-text'   => 'Configure work centers',
        ],

        'enable-work-order-dependencies' => [
            'label'       => 'Agizo la kazi Dependencies',
            'helper-text' => 'Weka mfuatano katika ambayo agizo la kazis unapaswa kuwa processed. Washa hii feature kutoka Miscellaneous kichupo ya kila BoM.',
        ],

        'enable-byproducts' => [
            'label'       => 'Byproducts',
            'helper-text' => 'Generate na-bidhaa wakati wa uzalishaji ( + B → C + D).',
        ],
    ],
];
