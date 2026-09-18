<?php

return [
    'not-enabled'       => 'Logistics is not enabled for company #:company. An administrator can enable it under Logistics > Configuration > Settings.',
    'company-mismatch'  => 'This :record belongs to a different company than its :related.',
    'uninstall-blocked' => 'Logistics can’t be uninstalled while :count shipment(s) exist: uninstalling deletes all logistics data. Back up the database, then set LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true to proceed.',

    'invalid-transition' => 'A shipment cannot go from “:from” to “:to”.',
];
