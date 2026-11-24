<?php

return [
    'title' => 'Equipes',

    'navigation' => [
        'title' => 'Equipes',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nome',
            'created-by' => 'Criado por',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipe atualizada',
                    'body'  => 'A equipe foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipe excluída',
                    'body'  => 'A equipe foi excluída com sucesso.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipes criadas',
                    'body'  => 'As equipes foram criadas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
        ],
    ],
];
