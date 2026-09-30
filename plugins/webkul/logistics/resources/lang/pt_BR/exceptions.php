<?php

return [
    'not-enabled'       => 'A Logística não está ativada para a empresa nº :company. Um administrador pode ativá-la em Logística > Configuração > Configurações.',
    'company-mismatch'  => 'Este :record pertence a uma empresa diferente de seu :related.',
    'uninstall-blocked' => 'A Logística não pode ser desinstalada enquanto existirem :count remessa(s): a desinstalação exclui todos os dados de logística. Faça backup do banco de dados e depois defina LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true para continuar.',

    'invalid-transition'      => 'Uma remessa não pode passar de “:from” para “:to”.',
    'invalid-trip-transition' => 'Uma viagem não pode passar de “:from” para “:to”.',

    'trip-no-vehicle'              => 'Atribua um veículo a esta viagem antes de despachá-la.',
    'trip-no-driver'               => 'Atribua um motorista a esta viagem antes de despachá-la.',
    'trip-inactive-vehicle'        => 'O veículo :vehicle está arquivado e não pode ser despachado.',
    'trip-inactive-driver'         => 'O motorista :driver está arquivado e não pode ser despachado.',
    'trip-expired-license'         => 'A CNH do motorista :driver venceu em :date.',
    'trip-shipment-not-confirmed'  => 'A remessa :shipment deve ser confirmada antes de poder ser adicionada a uma viagem.',

    'nothing-to-invoice' => 'A remessa :shipment não tem cobranças faturáveis restantes para faturar.',

    'receipt-required' => 'As despesas da categoria “:category” precisam de um comprovante anexado antes de serem enviadas ou aprovadas.',

    'expense-not-attributable' => 'Uma despesa deve estar vinculada a uma remessa, uma viagem ou um veículo antes de ser enviada ou aprovada.',

    'nothing-to-bill'           => 'A remessa :shipment não tem despesas aprovadas restantes para faturar.',
    'missing-partner'           => 'A despesa “:expense” não tem beneficiário, portanto não há ninguém para quem emitir a fatura.',
    'missing-employee-contact'  => ':employee não tem um registro de contato, portanto o reembolso não pode ser faturado para essa pessoa. Primeiro adicione um contato ao funcionário.',
    'missing-bill-journal'      => 'Esta empresa não tem um diário de faturas de fornecedor definido. Escolha um em Logística > Configuração > Configurações.',
    'missing-expense-account'   => 'Esta empresa não tem uma conta de despesa padrão definida. Escolha uma em Logística > Configuração > Configurações.',
];
