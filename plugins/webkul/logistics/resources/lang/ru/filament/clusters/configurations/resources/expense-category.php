<?php

return [
    'navigation' => [
        'title' => 'Категории расходов',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'Требуется чек',
            'is-subcontracting'        => 'Затраты субподрядчика или перевозчика',
            'is-subcontracting-helper' => 'Затраты этой категории оплачиваются поставщику (перевозчику, агенту), а счета выставляются по каждому поставщику.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'Требуется чек',
            'is-subcontracting' => 'Субподряд',
        ],
    ],
];
