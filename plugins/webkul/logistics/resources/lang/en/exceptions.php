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

    'receipt-required' => 'Expenses in the “:category” category need a receipt attached before they can be submitted or approved.',

    'expense-not-attributable' => 'An expense must be linked to a shipment, a trip or a vehicle before it can be submitted or approved.',

    'nothing-to-bill'           => 'Shipment :shipment has no approved expenses left to bill.',
    'missing-partner'           => 'Expense “:expense” has no payee, so there is nobody to make the bill out to.',
    'missing-employee-contact'  => ':employee has no contact record, so a reimbursement cannot be billed to them. Add a contact on the employee first.',
    'missing-bill-journal'      => 'This company has no vendor bill journal set. Choose one under Logistics > Configuration > Settings.',
    'missing-expense-account'   => 'This company has no default expense account set. Choose one under Logistics > Configuration > Settings.',
];
