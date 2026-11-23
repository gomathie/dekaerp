<?php

return [
    'not-enabled'       => 'Логистика не включено для компания #:company. администратор можно включить это менее Логистика > Конфигурация > Настройки.',
    'company-mismatch'  => 'Этот :record belongs до different компания чем его :related.',
    'uninstall-blocked' => 'Логистика можно’t быть uninstalled пока :count отправка(s) exist: uninstalling deletes все логистика data. Назад вверх database, затем задать Логистика_Разрешить_UNINSTALL_С_DATA=да до proceed.',

    'invalid-transition'      => 'отправка нельзя go от “:from” до “:to”.',
    'invalid-trip-transition' => 'рейс нельзя go от “:from” до “:to”.',

    'trip-no-vehicle'              => 'Назначить транспорт до этот рейс до dispatching это.',
    'trip-no-driver'               => 'Назначить водитель до этот рейс до dispatching это.',
    'trip-inactive-vehicle'        => 'Транспорт :vehicle в архиве и нельзя быть dispatched.',
    'trip-inactive-driver'         => 'Водитель :driver в архиве и нельзя быть dispatched.',
    'trip-expired-license'         => 'Водитель :driver’s licence expired вкл. :date.',
    'trip-shipment-not-confirmed'  => 'Отправка :shipment нужно быть подтверждён до это можно быть added до рейс.',

    'nothing-to-invoice' => 'Отправка :shipment есть нет billable charges осталось до счёт.',

    'receipt-required' => 'Расходы в “:category” категория need приход attached до они можно быть submitted или утверждён.',

    'expense-not-attributable' => 'расход нужно быть linked до отправка, рейс или транспорт до это можно быть submitted или утверждён.',

    'nothing-to-bill'           => 'Отправка :shipment есть нет утверждён расходы осталось до счёт поставщика.',
    'missing-partner'           => 'Расход “:expense” есть нет payee, поэтому nobody до сделать счёт поставщика вне до.',
    'missing-employee-contact'  => ':employee есть нет контакт запись, поэтому reimbursement нельзя быть billed до их. Добавить контакт вкл. сотрудник первый.',
    'missing-bill-journal'      => 'Этот компания есть нет счёт поставщика журнал задать. Выбрать one менее Логистика > Конфигурация > Настройки.',
    'missing-expense-account'   => 'Этот компания есть нет по умолчанию расход счёт задать. Выбрать one менее Логистика > Конфигурация > Настройки.',
];
