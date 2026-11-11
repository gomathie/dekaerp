<?php

return [
    'actions' => [
        'clock-in'  => 'Registrar entrada',
        'clock-out' => 'Registrar saída',
        'correct'   => 'Corrigir presença',
        'export'    => 'Exportar presença',
    ],
    'capture' => [
        'off'      => 'Desativada',
        'optional' => 'Opcional',
        'required' => 'Obrigatória',
    ],
    'exceptions' => [
        'not-enabled' => 'A presença não está ativada para a empresa :company.',
    ],
    'notifications' => [
        'failed' => 'Não foi possível concluir. A alteração não foi guardada.',
    ],
    'statuses' => [
        'clocked-in'     => 'Registado desde :time',
        'not-clocked-in' => 'Sem registo',
        'on-leave'       => 'De licença',
    ],
    'validation' => [
        'check-out-order'     => 'A saída tem de ser posterior à entrada.',
        'location-required'   => 'É necessária uma localização para registar.',
        'no-employee'         => 'Não tem ficha de colaborador nesta empresa.',
        'not-allowed'         => 'Só pode corrigir a presença da sua equipa.',
        'not-clocked-in'      => 'Não tem registo de entrada.',
        'not-your-attendance' => 'Só pode registar por si mesmo.',
        'reason-required'     => 'Uma correção precisa de um motivo.',
    ],
];
