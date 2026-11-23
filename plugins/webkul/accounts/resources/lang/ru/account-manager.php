<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'Пожалуйста provide действительно Клиент до proceed с Счёт клиенту validation.',
        'vendor-required'      => 'Пожалуйста provide действительно Поставщик до proceed с Счёт поставщика validation.',
        'bank-archived'        => 'выбрано Контрагент Банк attached до этот счёт в архиве.',
        'negative-amount'      => 'Счёт можно не быть подтверждён с negative итого сумма.',
        'date-required'        => 'Пожалуйста provide действительно Счёт поставщика/Возврат Дата до proceed с Счёт поставщика/Возврат validation.',
        'currency-archived'    => 'Вы нельзя подтвердить счёт с в архиве валюта.',
        'account-deprecated'   => 'One или больше строки в этот счёт с помощью deprecated счета.',
        'lines-required'       => 'Пожалуйста добавить в наименее one строка до счёт.',
        'draft-state-required' => 'Только счета в черновик состояние можно быть подтверждён.',
        'journal-archived'     => 'Вы нельзя подтвердить счёт с в архиве журнал.',
        'unbalanced'           => 'Этот проводка unbalanced и нельзя быть проведён. Итого дебет нужно equal итого кредит.',
    ],

    'change-reason' => [
        'required' => 'Give причина для changing подтверждён документ.',
        'field'    => 'Причина для изменить',
        'helper'   => 'Saved в документ история, для пример "Клиент asked для 10% скидка".',
        'reset'    => 'Сбросить до черновик. Причина: :reason',
        'cancelled' => 'Отменён. Причина: :reason',
        'api'      => 'Изменено через API',
    ],

    'lock-date' => [
        'locked'        => 'Проводки dated вкл. или до :date заблокировано. Ask ваш администратор до переместить lock дата первый.',
        'not-allowed'   => 'Только Компания Администратор или accountant с unlock право можно изменить lock дата.',
        'future'        => 'lock дата нельзя быть в будущие.',
        'action'        => 'Lock дата',
        'field'         => 'Lock проводки вверх до',
        'helper'        => 'Проведён проводки dated вкл. или до этот дата нельзя быть изменено. Отпуск пусто до unlock все periods.',
        'current'       => 'Currently заблокировано вверх до :date.',
        'none'          => 'Нет период заблокировано.',
        'saved'         => 'Lock дата saved',
    ],

    'documents' => [
        'titles' => [
            'invoice'     => 'Счёт Ид. #:name',
            'bill'        => 'Счёт поставщика Ид. #:name',
            'refund'      => 'Возврат Ид. #:name',
            'credit-note' => 'Кредит-нота Ид. #:name',
        ],

        'labels' => [
            'invoice-date'          => 'Дата счёта',
            'bill-date'             => 'Дата',
            'refund-date'           => 'Возврат Дата',
            'credit-note-date'      => 'Кредит-нота Дата',
            'source'                => 'Источник',
            'due-date'              => 'Срок оплаты',
            'product'               => 'Товар',
            'quantity'              => 'Количество',
            'unit'                  => 'Единица',
            'unit-price'            => 'Цена за единицу',
            'subtotal'              => 'Промежуточный итог',
            'tax'                   => 'Налог',
            'discount'              => 'Скидка',
            'grand-total'           => 'Grand Итого',
            'payment-information'   => 'Оплата Сведения',
            'payment-communication' => 'Оплата Сообщения',
            'account-details'       => 'вкл. этот счёт подробности:',
        ],
    ],
];
