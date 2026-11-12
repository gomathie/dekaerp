<?php

return [
    'navigation' => [
        'title' => 'Configurações de presença',
    ],
    'title'      => 'Configurações de presença',
    'subheading' => 'Presença de :company. Ative aqui e os funcionários poderão registrar ponto pelo painel.',
    'no-company' => 'Escolha uma empresa para gerenciar suas configurações de presença.',
    'sections'   => [
        'status' => 'Status',
        'rules'  => 'Regras',
    ],
    'status' => [
        'enabled'  => 'Ativada para esta empresa',
        'disabled' => 'Desativada para esta empresa',
    ],
    'fields' => [
        'auto-checkout'         => 'Saída automática após',
        'auto-checkout-help'    => 'Uma saída esquecida é fechada após estas horas e marcada para revisão.',
        'capture-location'      => 'Localização no registro',
        'capture-location-help' => 'Desativada: nada é registrado. Opcional: registrada se o dispositivo compartilhar. Obrigatória: o registro exige.',
        'late-tolerance'        => 'Atrasado após',
        'overtime-tolerance'    => 'Hora extra após',
    ],
    'units' => [
        'hours'   => 'horas',
        'minutes' => 'minutos',
    ],
    'actions' => [
        'save'   => 'Salvar',
        'enable' => [
            'label'        => 'Ativar',
            'heading'      => 'Ativar a presença?',
            'description'  => 'Funcionários desta empresa com cadastro de funcionário podem registrar ponto pelo painel. Gestores veem sua equipe.',
            'notification' => 'A presença está ativada para esta empresa.',
        ],
        'disable' => [
            'label'        => 'Desativar',
            'heading'      => 'Desativar a presença?',
            'description'  => 'O registro de ponto para e as telas de presença ficam ocultas. Os registros existentes são mantidos.',
            'notification' => 'A presença está desativada para esta empresa.',
        ],
    ],
    'notifications' => [
        'saved' => 'Configurações de presença salvas.',
    ],
];
