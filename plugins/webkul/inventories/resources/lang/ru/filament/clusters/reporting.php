<?php

return [
    'navigation' => [
        'title' => 'Отчёты',
        'group' => 'Запасы',
    ],

    'moves' => [
        'navigation' => [
            'title' => 'История движений',
        ],

        'filters' => [
            'product-category'     => 'Категория товаров',
            'source-location'      => 'Исходное местоположение',
            'destination-location' => 'Местоположение назначения',
            'package'              => 'Упаковка',
            'lot'                  => 'Партия/серийный номер',
            'package-type'         => 'Тип упаковки',
        ],

        'groups' => [
            'product'   => 'Товар',
            'status'    => 'Статус',
            'date'      => 'Дата',
            'operation' => 'Операция',
            'location'  => 'Местоположение',
            'category'  => 'Категория',
        ],
    ],

    'quantities' => [
        'navigation' => [
            'title' => 'Местоположения',
        ],

        'filters' => [
            'warehouse'        => 'Склад',
            'location'         => 'Местоположение',
            'product-category' => 'Категория товаров',
            'storage-category' => 'Категория хранения',
            'package'          => 'Упаковка',
            'lot'              => 'Партия/серийный номер',
            'package-type'     => 'Тип упаковки',
        ],

        'groups' => [
            'product'          => 'Товар',
            'product-category' => 'Категория товаров',
            'location'         => 'Местоположение',
            'storage-category' => 'Категория хранения',
            'lot'              => 'Партия/серийный номер',
            'package'          => 'Упаковка',
            'company'          => 'Компания',
        ],
    ],
];
