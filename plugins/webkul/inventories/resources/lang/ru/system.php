<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Nothing до чек availability для.',
        ],

        'cancel-move' => [
            'already-done' => 'Вы нельзя отмена запас переместить тот есть задать до \'Done\'. Создать return в заказ до reverse moves который took place.',
        ],

        'unreserve-move' => [
            'already-done' => "Вы можно не unreserve запас переместить тот есть задать до 'Done'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'количество завершён для товар ":product" doesn\'t respect rounding precision defined вкл. единица измерения ":unit". Пожалуйста изменить количество завершён или rounding precision ваш единица measure.',
            'no-negative-quantities'     => 'Нет negative quantities разрешено',
            'missing-lot-serial-number'  => "Вы need до supply Партия/Серийный номер Номер для товар:n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "Нет rule есть найдено до replenish \":product\" в \":location\".nVerify маршруты конфигурация вкл. товар.",
            'no-source-location' => 'Нет источник место defined вкл. запас rule: :name!',
            'no-vendor-price'    => 'нет matching поставщик цена до generate заказ на закупку для товар :product (нет поставщик defined, minimum количество не reached, dates не действительно,...). Go вкл. товар форма и завершить список поставщики.',
        ],

        'return' => [
            'origin' => 'Return :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'Reserving negative количество не разрешено.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'Количество или Зарезервировано Количество следует быть задать.',
        'removal-strategy-not-implemented' => 'Removal strategy :strategy не implemented.',
        'unreserve-more-than-stock'        => 'Это не possible до unreserve больше товары :name чем вы есть в запас.',
    ],

    'product' => [
        'endless-loop-rule' => "Недействительно rule's конфигурация, следующий rule causes endless loop: :name",
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'количество завершён для товар :product doesn\'t respect rounding precision defined вкл. единица измерения :unit. Пожалуйста изменить количество завершён или rounding precision ваш единица measure.',
        'split-done-or-cancel'       => 'Вы нельзя split запас переместить тот есть задать до \'Done\' или \'Cancel\'.',
        'split-draft'                => 'Вы нельзя split черновик переместить. Это needs до быть подтверждён первый.',
        'serial-already-assigned'    => 'серийный номер номер есть уже назначен до Товар: :product, Серийный номер Номер: :serial_number',

        'cross-company' => [
            'title' => 'Cross-компания перевод не разрешено',
            'body'  => 'перевод нельзя переместить запас directly между места тот belong до different компании (:source и :destination). Inter-компания перемещения не supported ещё.',
        ],
    ],

    'rule' => [
        'delay-on'     => 'Delay вкл. :name',
        'days'         => '+ :days день(s)',
        'time-horizon' => 'Time Horizon',
    ],
];
