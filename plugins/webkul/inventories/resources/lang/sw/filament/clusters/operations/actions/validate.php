<?php

return [
    'label'             => 'Validate',
    'modal-heading'     => 'Unda Rudi Agizo?',
    'modal-description' => 'Unda backorder ikiwa iliyobaki bidhaa ita kuwa processed later. Ikiwa si, si generate backorder.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'Hakuna Backorder',
        ],
    ],

    'notification' => [
        'error' => [
            'title' => 'Validation Imeshindwa',
        ],

        'warning' => [
            'lines-missing' => [
                'title' => 'Hakuna quantities ni imehifadhiwa',
                'body'  => 'Hakuna quantities ni imehifadhiwa kwa uhamisho.',
            ],

            'no-quantities-reserved' => [
                'title' => 'Hakuna quantities ni imehifadhiwa',
                'body'  => 'Hakuna quantities ni imehifadhiwa kwa uhamisho.',
            ],

            'lot-missing' => [
                'title' => 'Supply Kundi/Nambari ya mfululizo Nambari',
                'body'  => 'Wewe need hadi supply Kundi/Nambari ya mfululizo Nambari kwa bidhaa :products.',
            ],

            'serial-qty' => [
                'title' => 'Nambari ya mfululizo Nambari Tayari Amekabidhiwa',
                'body'  => 'nambari ya mfululizo nambari ina tayari amekabidhiwa hadi another bidhaa.',
            ],

            'partial-package' => [
                'title' => 'Unaweza si hamisha ile ile kifurushi content',
                'body'  => 'Wewe haiwezekani hamisha ile ile kifurushi contents zaidi kuliko mara moja ndani ya single uhamisho au split kifurushi kati ya two maeneo.',
            ],
        ],
    ],
];
