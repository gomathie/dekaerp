<?php

return [
    'label'             => 'Validate',
    'modal-heading'     => 'Создать Назад Заказ?',
    'modal-description' => 'Создать backorder если осталось товары быть processed later. Если не, не generate backorder.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'Нет Backorder',
        ],
    ],

    'notification' => [
        'error' => [
            'title' => 'Validation Не удалось',
        ],

        'warning' => [
            'lines-missing' => [
                'title' => 'Нет quantities зарезервировано',
                'body'  => 'Нет quantities зарезервировано для перевод.',
            ],

            'no-quantities-reserved' => [
                'title' => 'Нет quantities зарезервировано',
                'body'  => 'Нет quantities зарезервировано для перевод.',
            ],

            'lot-missing' => [
                'title' => 'Supply Партия/Серийный номер Номер',
                'body'  => 'Вы need до supply Партия/Серийный номер Номер для товары :products.',
            ],

            'serial-qty' => [
                'title' => 'Серийный номер Номер Уже Назначен',
                'body'  => 'серийный номер номер есть уже назначен до another товар.',
            ],

            'partial-package' => [
                'title' => 'Можно не переместить тот же упаковка content',
                'body'  => 'Вы нельзя переместить тот же упаковка contents больше чем один раз в пределах single перевод или split упаковка между two места.',
            ],
        ],
    ],
];
