<?php

return [
    'title'           => 'Registro de atividades',
    'navigation'      => 'Registro de atividades',
    'group'           => 'Geral',
    'system'          => 'Sistema',
    'export'          => 'Exportar',
    'export-complete' => 'A exportação do registro de atividades está pronta: :count linhas.',

    'columns' => [
        'when'    => 'Quando',
        'who'     => 'Quem',
        'record'  => 'Registro',
        'action'  => 'Ação',
        'changes' => 'Alterações',
    ],

    'filters' => [
        'record-type' => 'Tipo de registro',
        'from'        => 'De',
        'until'       => 'Até',
    ],

    'events' => [
        'created'      => 'Criado',
        'updated'      => 'Alterado',
        'deleted'      => 'Excluído',
        'soft_deleted' => 'Arquivado',
        'hard_deleted' => 'Excluído',
        'restored'     => 'Restaurado',
        'reset'        => 'Voltou para rascunho',
        'cancelled'    => 'Cancelado',
    ],
];
