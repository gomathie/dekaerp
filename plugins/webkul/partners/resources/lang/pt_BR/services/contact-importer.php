<?php

return [
    'errors' => [
        'empty'           => 'O arquivo não contém linhas.',
        'no-columns'      => 'Nenhuma coluna reconhecida na linha de cabeçalho. Colunas esperadas: :columns.',
        'missing-name'    => 'O arquivo precisa ter uma coluna "name".',
        'max-rows'        => 'Apenas as primeiras :max linhas foram importadas. Divida o arquivo e envie o restante.',
        'row'             => 'Linha :row: :reason',
        'formula'         => 'Linha :row: o valor ":column" começa com um caractere que a planilha interpreta como fórmula. Remova-o e envie novamente.',
        'duplicate-email' => 'Linha :row: já existe um contato com o e-mail :email.',
        'unknown-country' => 'Linha :row: nenhum país corresponde a ":country".',
        'unknown-state'   => 'Linha :row: nenhum estado corresponde a ":state".',
        'unknown-parent'  => 'Linha :row: nenhum contato existente corresponde à empresa ":parent".',
    ],
];
