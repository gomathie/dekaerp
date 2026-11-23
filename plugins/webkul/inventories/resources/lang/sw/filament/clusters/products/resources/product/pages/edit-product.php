<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Hitilafu updating tracking',
                    'body'  => 'Wewe unaweza si badilisha ghala tracking ya bidhaa hiyo ilikuwa tayari inatumika.',
                ],

                'reordering-rules' => [
                    'title' => 'Hitilafu updating bidhaa',
                    'body'  => 'Wewe bado ina baadhi inatumika reordering rules washa hii bidhaa. Tafadhali archive au futa wao kwanza.',
                ],

                'reserved' => [
                    'title' => 'Hitilafu updating tracking',
                    'body'  => 'Wewe unaweza si badilisha ghala tracking ya bidhaa hiyo ni currently imehifadhiwa washa hisa hamisha. Ikiwa wewe need hadi badilisha ghala tracking, wewe unapaswa kwanza unreserve hisa hamisha.',
                ],

                'qty-not-zero' => [
                    'title' => 'Hitilafu updating tracking',
                    'body'  => 'Inapatikana kiasi unapaswa kuwa weka hadi zero kabla ya changing ghala tracking.',
                ],

                'track-by-update' => [
                    'title' => 'Hitilafu updating tracking',
                    'body'  => 'Wewe ina bidhaa(s) katika hisa hiyo ina hakuna kundi/nambari ya mfululizo nambari. Wewe unaweza kabidhi kundi/nambari ya mfululizo numbers na doing ghala adjustment.',
                ],
            ],
        ],
    ],
];
