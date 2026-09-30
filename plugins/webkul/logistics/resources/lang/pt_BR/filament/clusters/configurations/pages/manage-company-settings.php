<?php

return [
    'navigation' => [
        'title' => 'Configurações',
    ],

    'title'      => 'Configurações de Logística',
    'subheading' => 'Configurações para :company. A Logística é ativada e desativada por empresa.',
    'no-company' => 'Primeiro selecione uma empresa: as configurações de Logística são mantidas por empresa.',

    'sections' => [
        'status'     => 'Status',
        'delivery'   => 'Comprovante de entrega',
        'operations' => 'Operações',
        'accounting' => 'Contabilidade',
    ],

    'status' => [
        'enabled'  => 'A Logística está ativada para esta empresa',
        'disabled' => 'A Logística não está ativada para esta empresa',
    ],

    'readiness' => [
        'label' => 'Prontidão',
        'ok'    => 'Tudo que a Logística precisa está disponível.',
    ],

    'fields' => [
        'require-pod-for-delivery'    => 'Exigir comprovante de entrega para marcar uma remessa como entregue',
        'require-pod-photo'           => 'Exigir uma foto com o comprovante de entrega',
        'stop-link-ttl-hours'         => 'Validade do link da parada (horas)',
        'stop-link-ttl-hours-help'    => 'Por quanto tempo um link de comprovante de entrega permanece utilizável depois de emitido. Um prazo menor é mais seguro: o link é uma credencial e circula em uma URL.',
        'stop-link-rate-limit'        => 'Solicitações do link da parada por minuto',
        'stop-link-rate-limit-help'   => 'Por link e por minuto. Deixe em branco para usar o padrão. Aumente somente se o acesso estiver sendo negado aos seus motoristas.',
        'capture-driver-on-pod'       => 'Registrar o motorista nos comprovantes de entrega',
        'capture-driver-on-pod-help'  => 'Obtém o motorista da viagem da parada para que o comprovante mostre quem estava no local da entrega.',
        'capture-recipient-id'        => 'Solicitar o documento do destinatário',
        'capture-recipient-id-help'   => 'Adiciona um campo de documento obrigatório ao registrar o comprovante. Colete esse dado somente quando houver motivo para isso.',
        'default-service-type'        => 'Tipo de serviço padrão',
        'capacity-check'              => 'Verificação da capacidade do veículo',
        'overdue-grace-minutes'       => 'Em atraso após (minutos além do horário previsto)',
        'free-waiting-minutes'        => 'Tempo de espera gratuito em uma parada (minutos)',
        'free-waiting-minutes-helper' => 'Após esse tempo, é sugerida uma cobrança por espera. Ela nunca é adicionada automaticamente.',
        'invoice-journal'             => 'Diário para faturas de clientes',
        'bill-journal'                => 'Diário para faturas de fornecedores',
        'default-expense-account'     => 'Conta de despesa padrão',
        'expense-approval-required'   => 'As despesas precisam de aprovação antes do faturamento',
    ],

    'actions' => [
        'save' => 'Salvar',

        'enable' => [
            'label'                     => 'Ativar Logística',
            'heading'                   => 'Ativar Logística para esta empresa?',
            'description'               => 'Os menus e registros de Logística ficam disponíveis para os usuários desta empresa que têm permissões de Logística.',
            'description-with-problems' => 'A Logística pode ser ativada, mas alguns recursos não funcionarão até que estes problemas sejam corrigidos: :problems',
            'notification'              => 'Logística ativada',
        ],

        'disable' => [
            'label'        => 'Desativar Logística',
            'heading'      => 'Desativar Logística para esta empresa?',
            'description'  => 'A Logística fica oculta e nenhum novo registro pode ser criado para esta empresa. Os dados existentes são mantidos e a reativação restaura o acesso.',
            'notification' => 'Logística desativada',
        ],
    ],

    'notifications' => [
        'saved' => 'Configurações salvas',
    ],
];
