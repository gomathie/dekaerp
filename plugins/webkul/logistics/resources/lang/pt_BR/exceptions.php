<?php

return [
    'not-enabled'       => 'A Logística não está ativada para a empresa nº :company. Um administrador pode ativá-la em Logística > Configuração > Configurações.',
    'company-mismatch'  => 'Este :record pertence a uma empresa diferente de seu :related.',
    'uninstall-blocked' => 'A Logística não pode ser desinstalada enquanto existirem :count remessa(s): a desinstalação exclui todos os dados de logística. Faça backup do banco de dados e depois defina LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true para continuar.',
];
