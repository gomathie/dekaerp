<?php

return [
    'not-enabled'       => 'Logística no está activada para la empresa n.º :company. Un administrador puede activarla en Logística > Configuración > Configuración.',
    'company-mismatch'  => 'Este :record pertenece a una empresa distinta de su :related.',
    'uninstall-blocked' => 'No se puede desinstalar Logística mientras existan :count envío(s): al desinstalarla se eliminan todos los datos de logística. Haga una copia de seguridad de la base de datos y después establezca LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true para continuar.',
];
