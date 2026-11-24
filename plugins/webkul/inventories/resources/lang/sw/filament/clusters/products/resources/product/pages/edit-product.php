<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Hitilafu ya kusasisha ufuatiliaji',
                    'body'  => 'Huwezi kubadilisha ufuatiliaji wa bidhaa ghalani kwa bidhaa ambayo tayari imetumika.',
                ],

                'reordering-rules' => [
                    'title' => 'Hitilafu ya kusasisha bidhaa',
                    'body'  => 'Bidhaa hii bado ina kanuni hai za kuagiza upya. Tafadhali ziweke kumbukumbu au uzifute kwanza.',
                ],

                'reserved' => [
                    'title' => 'Hitilafu ya kusasisha ufuatiliaji',
                    'body'  => 'Huwezi kubadilisha ufuatiliaji wa bidhaa ambayo imetengwa kwenye mwendo wa bidhaa. Ukihitaji kuubadilisha, ondoa kwanza utengaji kwenye mwendo huo.',
                ],

                'qty-not-zero' => [
                    'title' => 'Hitilafu ya kusasisha ufuatiliaji',
                    'body'  => 'Idadi inayopatikana inapaswa kuwa sifuri kabla ya kubadilisha ufuatiliaji.',
                ],

                'track-by-update' => [
                    'title' => 'Hitilafu ya kusasisha ufuatiliaji',
                    'body'  => 'Una bidhaa ghalani zisizo na kundi/nambari ya mfululizo. Unaweza kuziwekea kwa kufanya marekebisho ya bidhaa ghalani.',
                ],
            ],
        ],
    ],
];
