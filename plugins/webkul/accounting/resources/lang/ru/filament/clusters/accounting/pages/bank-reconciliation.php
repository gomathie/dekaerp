<?php

return [
    'navigation' => [
        'title' => 'Банк Сверка',
    ],

    'import' => [
        'label'       => 'Импорт выписка',
        'description' => 'Загрузить CSV файл от ваш банк. Give номер каждый колонка, counting от 1 вкл. осталось.',
        'journal'     => 'Банковский счёт',
        'file'        => 'Выписка файл (CSV)',
        'name'        => 'Выписка название',
        'date-format' => 'Дата формат',
        'has-header'  => 'Первый строка header',
        'amount-help' => 'Или отпуск пусто и give Поступления и Выплаты колонки.',
        'done'        => ':count строки imported.',

        'columns' => [
            'date'      => 'Дата колонка',
            'label'     => 'Описание колонка',
            'amount'    => 'Сумма колонка',
            'money_in'  => 'Поступления колонка',
            'money_out' => 'Выплаты колонка',
            'partner'   => 'Клиент или поставщик колонка',
            'reference' => 'Ссылка колонка',
        ],
    ],

    'columns' => [
        'date'    => 'Дата',
        'label'   => 'Описание',
        'partner' => 'Клиент или поставщик',
        'journal' => 'Банковский счёт',
        'amount'  => 'Сумма',
        'status'  => 'Статус',
        'payment' => 'Оплата',
    ],

    'status' => [
        'reconciled'   => 'Reconciled',
        'to-reconcile' => 'До сверить',
    ],

    'match' => [
        'label'       => 'Совпадение оплата',
        'description' => 'Оплаты в этот банковский счёт для тот же сумма тот всё ещё waiting для банк.',
        'payment'     => 'Оплата',
        'none'        => 'Нет оплата совпадения. Регистрация оплата вкл. счёт первый, или book строка до счёт.',
        'done'        => 'Строка matched. оплата сейчас оплачен.',
    ],

    'book' => [
        'label'       => 'Book до счёт',
        'description' => 'Для банк charges, interest или перемещения: posts строка против счёт вы выбрать.',
        'account'     => 'Счёт',
        'entry-label' => 'Подпись',
        'done'        => 'Строка booked.',
    ],

    'empty' => [
        'heading'     => 'Nothing до сверить',
        'description' => 'Импорт выписка от ваш банк до начать.',
    ],
];
