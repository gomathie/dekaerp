<?php

return [
    'not-enabled'       => 'Logistics is not enabled for company #:company. An administrator can enable it under Logistics > Configuration > Settings.',
    'company-mismatch'  => 'This :record belongs to a different company than its :related.',
    'uninstall-blocked' => 'Logistics can’t be uninstalled while :count shipment(s) exist: uninstalling deletes all logistics data. Back up the database, then set LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true to proceed.',

    'invalid-transition'      => 'A shipment cannot go from “:from” to “:to”.',
    'invalid-trip-transition' => 'A trip cannot go from “:from” to “:to”.',

    'trip-no-vehicle'              => 'Assign a vehicle to this trip before dispatching it.',
    'trip-no-driver'               => 'Assign a driver to this trip before dispatching it.',
    'trip-inactive-vehicle'        => 'Vehicle :vehicle is archived and cannot be dispatched.',
    'trip-inactive-driver'         => 'Driver :driver is archived and cannot be dispatched.',
    'trip-expired-license'         => 'Driver :driver’s licence expired on :date.',
    'trip-shipment-not-confirmed'  => 'Shipment :shipment must be confirmed before it can be added to a trip.',

    'nothing-to-invoice' => 'Shipment :shipment has no billable charges left to invoice.',
];
