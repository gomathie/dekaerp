<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Título',
                'type'         => 'Tipo',
                'name'         => 'Nome',
                'type'         => 'Tipo',
                'create-type'  => 'Criar tipo',
                'duration'     => 'Duração',
                'start-date'   => 'Data de início',
                'end-date'     => 'Data de término',
                'display-type' => 'Tipo de exibição',
                'description'  => 'Descrição',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Título',
            'start-date'   => 'Data de início',
            'end-date'     => 'Data de término',
            'display-type' => 'Tipo de exibição',
            'description'  => 'Descrição',
            'created-by'   => 'Criado por',
            'created-at'   => 'Criado em',
            'updated-at'   => 'Atualizado em',
        ],

        'groups' => [
            'group-by-type'         => 'Agrupar por tipo',
            'group-by-display-type' => 'Agrupar por tipo de exibição',
        ],

        'header-actions' => [
            'add-resume' => 'Adicionar currículo',
        ],

        'filters' => [
            'type'            => 'Tipo',
            'start-date-from' => 'Data inicial de',
            'start-date-to'   => 'Data inicial até',
            'created-from'    => 'Criado a partir de',
            'created-to'      => 'Criado até',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Item do currículo atualizado',
                    'body'  => 'O item do currículo foi atualizado com sucesso.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Item do currículo criado',
                    'body'  => 'O item do currículo foi criado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Item do currículo excluído',
                    'body'  => 'O item do currículo foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Itens do currículo excluídos',
                    'body'  => 'Os itens do currículo foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Título',
            'display-type' => 'Tipo de exibição',
            'type'         => 'Tipo',
            'description'  => 'Descrição',
            'duration'     => 'Duração',
            'start-date'   => 'Data de início',
            'end-date'     => 'Data de término',
        ],
    ],
];
