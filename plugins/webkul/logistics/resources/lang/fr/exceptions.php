<?php

return [
    'not-enabled'       => 'La logistique n’est pas activée pour la société n° :company. Un administrateur peut l’activer dans Logistique > Configuration > Paramètres.',
    'company-mismatch'  => 'Ce :record appartient à une société différente de son :related.',
    'uninstall-blocked' => 'La logistique ne peut pas être désinstallée tant qu’il existe :count expédition(s) : sa désinstallation supprime toutes les données logistiques. Sauvegardez la base de données, puis définissez LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true pour continuer.',
];
