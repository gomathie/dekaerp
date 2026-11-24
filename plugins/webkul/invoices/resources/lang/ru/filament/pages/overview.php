<?php

return [
    'navigation' => [
        'title' => 'Обзор',
    ],

    'title' => 'Обзор',

    'sections' => [
        'sales'     => 'Продажи',
        'purchases' => 'Закупки',
        'invoices'  => 'Счета & счета поставщиков',
    ],

    'filters' => [
        'sections'       => 'Показать',
        'all-sections'   => 'Все разделы',
        'date-range'     => 'Диапазон дат',
        'product'        => 'Товар',
        'all-products'   => 'Все товары',
        'category'       => 'Категория',
        'all-categories' => 'Все категории',
    ],

    'headline' => [
        'heading'         => 'В glance',
        'sales'           => 'Продажи',
        'purchases'       => 'Закупки',
        'invoices'        => 'Счета',
        'revenue'         => 'Выручка',
        'orders'          => '{0} Нет продажи|{1} 1 продажа|[2,*] :count продажи',
        'purchase-orders' => '{0} Нет заказы|{1} 1 заказ|[2,*] :count заказы',
        'open-quotations' => 'Открыт коммерческие предложения',
        'spend'           => 'Закупка spend',
        'open-rfqs'       => 'Открыт RFQs',
        'to-receive'      => 'До receive',
        'to-receive-hint' => 'Подтверждён заказы не ещё fully получено',
        'empty-period'    => 'Nothing в этот период. последние dated :date; выбрать longer период выше.',
        'to-collect'      => 'До collect',
        'to-pay'          => 'До pay',
        'overdue'         => '{0} Нет просрочен|{1} 1 просрочен|[2,*] :count просрочен',
    ],

    'attention' => [
        'heading'          => 'Needs attention',
        'empty'            => 'Все очистить. Nothing waiting вкл. вы.',
        'overdue-invoices' => '{1} 1 счёт просрочен|[2,*] :count счета просрочен',
        'overdue-bills'    => '{1} 1 счёт поставщика просрочен|[2,*] :count счета поставщиков просрочен',
        'bills-due-soon'   => '{1} 1 счёт поставщика due этот неделя|[2,*] :count счета поставщиков due этот неделя',
        'old-quotations'   => '{1} 1 коммерческое предложение waiting более 7 дни|[2,*] :count коммерческие предложения waiting более 7 дни',
        'rfqs-to-approve'  => '{1} 1 закупка до утвердить|[2,*] :count закупки до утвердить',
    ],

    'actions' => [
        'new-invoice'   => 'Новый счёт',
        'new-bill'      => 'Новое счёт поставщика',
        'new-quotation' => 'Новое коммерческое предложение',
        'new-rfq'       => 'Новое Запрос котировок',
    ],

    'cash' => [
        'heading'     => 'Поступления и вне',
        'in'          => 'Поступления',
        'out'         => 'Выплаты',
    ],

    'trend' => ':change vs предыдущий период',

    'period' => [
        'label'        => 'Период',
        'this-month'   => 'Этот месяц',
        'last-30-days' => 'Последний 30 дни',
        'year-to-date' => 'Год до дата',
        'last-year'    => 'Последний год',
        'custom'       => 'Свой',
        'compared'     => ':period, compared с :previous',
        'custom-hint'  => 'Pick любой другое dates менее Фильтры.',
    ],

    'modules' => [
        'heading' => 'По компания',
        'open'    => 'Открыт',
    ],

    'board' => [
        'updated'     => 'Обновлено :time',
        'orders'      => 'Продажи',
        'conversion'  => 'Quote → заказ',
        'by-supplier' => 'Spend от поставщик',
        'other'       => 'Другое',
        'paid'        => 'Оплачен',
        'open'        => 'Открыт',
        'overdue'     => 'Просрочен',
        'show'        => 'Показать',
        'tab-overdue' => 'Просрочен',
        'tab-soon'    => 'Due этот неделя',
        'tab-open'    => 'Все открыт',
        'nothing'     => 'Nothing здесь.',
        'customer'    => 'Клиент',
        'due'         => 'Due',
        'no-due-date' => 'Нет Срок оплаты',
        'due-today'   => 'Due сегодня',
        'due-on'      => 'Due :date',
        'days-late'   => '{1} 1 день late|[2,*] :count дни late',
    ],
];
