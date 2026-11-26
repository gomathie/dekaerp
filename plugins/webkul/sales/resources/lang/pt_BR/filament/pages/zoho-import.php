<?php

return [
    'navigation' => 'Importar do Zoho Books',
    'title'      => 'Importar do Zoho Books',
    'intro'      => 'Traga o histórico de uma empresa do Zoho Books: escolha a empresa, envie a exportação, confira como contas, impostos e contas de dinheiro serão associados e depois importe. Os documentos são registrados do mais antigo ao mais recente com os números do Zoho, e a página compara o Zoho com o DEKA.',
    'empty'      => 'Nenhuma importação ainda. Comece com “Enviar uma exportação do Zoho”.',
    'unused'     => ':count contas do Zoho não são usadas por nenhum documento e ficam de fora.',

    'propose' => [
        'label'       => 'Enviar uma exportação do Zoho',
        'description' => 'O arquivo de exportação (JSON, até 10 MB) é verificado e associado à empresa. Nada é registrado ainda.',
        'company'     => 'Empresa',
        'file'        => 'Exportação do Zoho Books (JSON)',
        'done'        => 'A associação está pronta. Confira abaixo e depois importe.',
    ],

    'propose-options' => [
        'numbering'      => 'Numeração dos novos documentos',
        'numbering-deka' => 'Série anual do DEKA (os documentos importados mantêm os números do Zoho)',
        'numbering-zoho' => 'Continuar o contador do Zoho',
        'skip-samples'   => 'Ignorar os contatos de demonstração do Zoho (“(Sample)”) que nenhum documento usa',
    ],

    'compare' => [
        'label'       => 'Comparar com uma nova exportação do Zoho',
        'description' => 'Para o período em paralelo: envie uma exportação recente do Zoho para compará-la com o que o DEKA tem agora, incluindo o que foi lançado direto no DEKA. Nada é gravado.',
        'queued'      => 'A comparação começou. Esta página acompanha o andamento.',
    ],

    'download' => [
        'label' => 'Baixar a comparação (CSV)',
    ],

    'unmatched' => [
        'none' => 'Toda fatura, pagamento e despesa está nos dois sistemas.',
    ],

    'modes' => [
        'import'  => 'Importação',
        'compare' => 'Comparação',
    ],

    'import' => [
        'label'       => 'Importar',
        'confirm'     => 'Importar para :company?',
        'description' => 'Todo o histórico é registrado nesta empresa. Não pode ser desfeito por aqui; uma importação interrompida continua de onde parou ao ser iniciada de novo.',
        'queued'      => 'A importação começou. Esta página acompanha o andamento.',
    ],

    'run' => [
        'heading'    => 'Importação para :company',
        'status'     => 'Situação',
        'started-by' => 'Iniciada por',
        'source'     => 'Na exportação',
        'working'    => 'Importando. Pode levar alguns minutos; a página se atualiza sozinha.',
    ],

    'statuses' => [
        'proposed' => 'Aguardando sua aprovação',
        'queued'   => 'Na fila',
        'running'  => 'Importando',
        'finished' => 'Concluída',
        'failed'   => 'Interrompida',
    ],

    'sections' => [
        'accounts'   => 'Contas',
        'taxes'      => 'Impostos',
        'banks'      => 'Contas bancárias e de caixa',
        'records'    => 'Produtos, contatos e numeração',
        'result'     => 'O que foi registrado',
        'unmatched'  => 'Documentos em apenas um sistema',
        'comparison' => 'Zoho comparado com o DEKA',
    ],

    'columns' => [
        'zoho'       => 'Zoho',
        'uses'       => 'Usos',
        'action'     => 'Ação',
        'deka'       => 'DEKA',
        'section'    => 'Seção',
        'row'        => 'Linha',
        'difference' => 'Diferença',
    ],

    'actions' => [
        'map'    => 'usar existente',
        'create' => 'criar',
        'unused' => 'não usada',
    ],

    'records' => [
        'items'          => 'Itens: :create a criar, :map associados a produtos existentes.',
        'contacts'       => 'Contatos: :create a criar, :map associados a contatos existentes.',
        'numbering-deka' => 'Novos :kind usam a série anual do DEKA; os importados mantêm os números do Zoho.',
        'numbering'      => 'Novos :kind continuam com :next.',
    ],

    'comparison' => [
        'all-ok'      => 'Todas as linhas conferem.',
        'differences' => ':count linhas diferem.',
    ],

    'errors' => [
        'empty-file' => 'Não foi possível ler o arquivo enviado.',
        'no-run'     => 'Envie uma exportação primeiro.',
    ],
];
