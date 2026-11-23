<?php

return [
    'validation' => [
        'upload-failed' => 'приход файл could не быть stored. Пожалуйста попробуйте снова.',
    ],
    'relation-manager' => [
        'title' => 'Затраты',

        'fields' => [
            'date'           => 'Дата',
            'category'       => 'Категория',
            'payee'          => 'Оплачен до',
            'amount'         => 'Сумма',
            'approved-total' => 'Утверждён',
            'state'          => 'Статус',
            'bill'           => 'Счёт поставщика',
            'not-billed'     => 'Не billed',
        ],
    ],

    'margin' => [
        'heading' => 'Выручка и стоимость',
        'revenue' => 'Выручка',
        'costs'   => 'Затраты',
        'margin'  => 'Маржа',
        'helper'  => 'Billable charges против утверждён затраты. Черновик и отклонён затраты не counted.',
    ],

    'actions' => [
        'post-bill' => [
            'label'          => 'Создать счёт поставщика',
            'heading'        => 'Создать черновик счёт поставщикаs?',
            'description'    => 'Каждый утверждён, unbilled стоимость вкл. этот отправка becomes черновик счёт поставщика, one за payee. Nothing проведён: финансы всё ещё reviews и posts их.',
            'cannot-post'    => 'Эти затраты нельзя быть billed ещё',
            'draft-reminder' => 'Создано как drafts. Review и post их в Учёт.',
            'notification'   => '{0}Nothing был осталось до счёт поставщика.|{1}One черновик счёт поставщика создано.|[2,*]:count черновик счёт поставщикаs создано.',
        ],
    ],
];
