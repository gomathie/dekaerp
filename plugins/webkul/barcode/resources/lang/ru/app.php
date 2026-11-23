<?php

return [
    'title' => 'Штрихкод',

    'navigation' => [
        'back'        => 'Назад',
        'home'        => 'Операции',
        'search'      => 'Поиск...',
        'label'       => 'Navigation',
        'open'        => 'Открыт navigation',
        'coming-soon' => 'Coming скоро',
    ],

    'auth' => [
        'login-title'       => 'Штрихкод Вход',
        'login-heading'     => 'Войти до Штрихкод',
        'login-subheading'  => 'Продолжить до штрихкод операции app.',
    ],

    'filament' => [
        'navigation' => [
            'group' => 'Штрихкод',
            'label' => 'Штрихкод App',
        ],
    ],

    'dashboard' => [
        'operations' => 'Операции',
        'empty'      => 'Нет операции доступно.',
    ],

    'operation-search' => [
        'placeholder'    => 'Scan или введите operation штрихкод...',
        'open'           => 'Открыт',
        'not-found'      => 'Нет активен operation найдено для этот штрихкод.',
        'multiple-found' => ':count matching операции найдено.',
    ],

    'transfers' => [
        'title' => 'Перемещения',
        'empty' => 'Нет перемещения найдено.',
    ],

    'adjustments' => [
        'title'             => 'Склад Adjustments',
        'subtitle'          => 'Количество запас от место, товар, или партия',
        'search'            => 'Scan или поиск от место, товар, партия, серийный номер...',
        'empty'             => 'Нет склад quantities найдено.',
        'location-scanned'  => 'Scanning место :location. Scan больше товары здесь или scan another место.',
        'location-cleared'  => 'Склад adjustment фильтры cleared.',
        'product-not-found' => 'Этот товар не доступно в текущий склад selection.',
        'lot-not-found'     => 'Этот партия или серийный номер не доступно в текущий склад selection.',
        'multiple-found'    => ':count matching склад quantities найдено.',
        'count-saved'       => 'Склад количество saved.',
        'count-applied'     => 'Склад adjustment применено.',
        'count-cleared'     => 'Склад количество cleared.',
        'counted'           => 'Counted',
        'on-hand'           => 'Вкл. вручную',
        'location'          => 'Место',
        'product'           => 'Товар',
        'lot-serial'        => 'Партия/Серийный номер',
        'clear-filters'     => 'Очистить фильтры',
        'apply'             => 'Применить',
        'clear'             => 'Очистить',
        'editor-title'      => 'Adjustment подробности',
        'editor-subtitle'   => 'Review запас подробности и обновить counted количество.',
        'editor-image'      => 'Склад количество изображение',
        'edit-tooltip'      => 'Изменить склад количество',
    ],

    'operation' => [
        'scan'                 => 'Scan товар, партия, упаковка, упаковка, или перевод',
        'manual-scan'          => 'Scan или поиск от товар, ссылка, штрихкод...',
        'search'               => 'Поиск товар, ссылка, штрихкод...',
        'moves'                => 'Moves',
        'source'               => 'От',
        'available'            => 'Доступно',
        'discard'              => 'Discard',
        'confirm'              => 'Подтвердить',
        'counted'              => 'Counted',
        'lot-serial'           => 'Партия/Серийный номер Номер',
        'stock-title'          => 'Количество в Запас',
        'empty-moves'          => 'Нет moves найдено.',
        'details-title'        => 'Переместить подробности',
        'settings-title'       => 'Переместить настройки',
        'pick-from'            => 'Pick От',
        'destination-location' => 'Назначение Место',
        'destination-package'  => 'Назначение Упаковка',
        'select-package'       => 'Выбрать упаковка',
        'stock-subtitle'       => 'Выбрать где иначе до pick товар от',
        'no-stock-locations'   => 'Нет запас места найдено.',
        'camera-unavailable'   => 'Camera недоступно',
        'submit-scan'          => 'Отправить scan',
        'image-alt'            => 'Переместить строка изображение',
        'edit-tooltip'         => 'Изменить переместить строка',
    ],

    'scan' => [
        'empty'                    => 'Введите или scan штрихкод.',
        'not-found'                => 'Нет matching штрихкод найдено.',
        'operation-matched'        => 'Перевод matched.',
        'product-not-on-operation' => 'Этот товар не part operation.',
        'package-matched'          => 'Упаковка matched.',
        'move-located'             => 'Переместить located. Введите counted количество.',
        'move-updated'             => 'Переместить количество обновлено.',
        'move-counted'             => 'Переместить marked как counted.',
    ],

    'actions' => [
        'confirm'                  => 'Подтвердить',
        'confirm-prompt'           => 'вы sure вы want до',
        'cancel'                   => 'Отмена',
        'check-availability'       => 'Чек Availability',
        'validate'                 => 'Validate',
        'return'                   => 'Return',
        'stay-on-transfer'         => 'Discard',
        'no-backorder'             => 'Нет Backorder',
        'backorder-title'          => 'Не завершено Перевод',
        'backorder-prompt'         => 'Если вы validate сейчас, осталось товары быть added до backorder.',
        'backorder-col-product'    => 'Товар',
        'backorder-col-done-todo'  => 'Завершён / До',
        'backorder-col-backorder'  => 'Backorder',
        'completed'                => 'Действие завершено.',
        'unsupported'              => 'Unsupported штрихкод действие.',
        'no-moves'                 => 'Этот operation есть нет moves.',
        'no-return-quantities'     => 'нет quantities до return.',
    ],
];
