<?php

return [
    'navigation' => [
        'title' => 'Informes',
    ],
    'common' => [
        'from-to'                  => ':report - Desde :from hasta :to',
        'expand-all'               => 'Expandir todo',
        'collapse-all'             => 'Contraer todo',
        'account'                  => 'Cuenta',
        'date'                     => 'Fecha',
        'communication'            => 'Comunicación',
        'partner'                  => 'Contacto',
        'journal'                  => 'Diario',
        'invoice-date'             => 'Fecha de factura',
        'due-date'                 => 'Fecha de vencimiento',
        'debit'                    => 'Débito',
        'credit'                   => 'Crédito',
        'balance'                  => 'Saldo',
        'total'                    => 'Total',
        'opening-balance'          => 'Saldo de apertura',
        'initial-balance'          => 'Saldo inicial',
        'end-balance'              => 'Saldo final',
        'not-due'                  => 'No vencido',
        'no-data'                  => 'No hay datos disponibles',
        'no-accounts-transactions' => 'No hay cuentas con transacciones en este período',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Balance general',
                'group' => 'Informes de estados',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'ACTIVOS',
                        'total-label' => 'Total ACTIVOS',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Activos corrientes',
                                'total-label' => 'Total activos corrientes',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Activos fijos',
                                'total-label' => 'Total activos fijos',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Activos no corrientes',
                                'total-label' => 'Total activos no corrientes',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'PASIVOS',
                        'total-label' => 'Total PASIVOS',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Pasivos corrientes',
                                'total-label' => 'Total pasivos corrientes',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Pasivos no corrientes',
                                'total-label' => 'Total pasivos no corrientes',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'PATRIMONIO',
                        'total-label' => 'Total PATRIMONIO',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Ganancias no asignadas',
                                'current-year'   => 'Ganancias no asignadas del año actual',
                                'previous-years' => 'Ganancias no asignadas de años anteriores',
                                'total-label'    => 'Total ganancias no asignadas',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Ganancias retenidas',
                                'total-label' => 'Total ganancias retenidas',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'PASIVOS + PATRIMONIO',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Pérdidas y ganancias',
                'group' => 'Informes de estados',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'INGRESOS',
                        'total-label'   => 'Total ingresos',
                        'empty-message' => 'No hay cuentas de ingresos con transacciones en este período',
                    ],
                    'expenses' => [
                        'title'         => 'GASTOS',
                        'total-label'   => 'Total gastos',
                        'empty-message' => 'No hay cuentas de gastos con transacciones en este período',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Libro mayor general',
                'group' => 'Informes de auditoría',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
        ],
        'tax-summary' => [
            'navigation' => [
                'title' => 'Resumen de impuestos',
                'group' => 'Informes de auditoría',
            ],
            'actions'  => ['export-excel' => 'Exportar a Excel'],
            'filters'  => ['date-range' => 'Rango de fechas'],
            'columns'  => ['tax' => 'Impuesto', 'rate' => 'Tasa', 'base' => 'Base imponible', 'amount' => 'Impuesto'],
            'sections' => ['sales' => 'En ventas (cobrado)', 'purchases' => 'En compras (pagado)'],
            'totals'   => [
                'collected'       => 'Total cobrado',
                'paid'            => 'Total pagado',
                'net'             => 'Neto',
                'net-payable'     => 'Impuesto neto a pagar',
                'net-reclaimable' => 'Impuesto neto a recuperar',
            ],
            'empty' => 'No hay impuestos en este periodo.',
            'note'  => 'A partir de facturas, facturas de proveedor, notas de crédito y reembolsos contabilizados con fecha en el periodo. Las notas de crédito y los reembolsos reducen los totales. Consulte con su contable antes de declarar.',
        ],
        'cash-flow' => [
            'navigation' => [
                'title' => 'Flujo de efectivo',
                'group' => 'Informes de estados',
            ],
            'actions'  => ['export-excel' => 'Exportar a Excel'],
            'filters'  => ['date-range' => 'Rango de fechas'],
            'columns'  => ['account' => 'Origen o destino', 'in' => 'Entradas', 'out' => 'Salidas', 'net' => 'Neto'],
            'sections' => ['operating' => 'Actividades de operación', 'investing' => 'Actividades de inversión', 'financing' => 'Actividades de financiación'],
            'totals'   => [
                'opening' => 'Efectivo al inicio del periodo',
                'section' => 'Efectivo neto',
                'net'     => 'Variación neta del efectivo',
                'closing' => 'Efectivo al final del periodo',
            ],
            'other' => 'Otros',
            'empty' => 'No hay movimientos de efectivo en este periodo.',
            'note'  => 'A partir de asientos contabilizados en cuentas de banco y caja. Cada asiento aparece bajo la cuenta de su contrapartida. Las transferencias entre sus propias cuentas de banco y caja se excluyen.',
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Balance de comprobación',
                'group' => 'Informes de auditoría',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Libro mayor de contactos',
                'group' => 'Informes de contactos',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'partners'   => 'Contactos',
                'journals'   => 'Diarios',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Antigüedad de cuentas por cobrar',
                'group' => 'Informes de contactos',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'as-of'         => 'A fecha de',
                'based-on'      => 'Basado en',
                'period-length' => 'Duración del período (días)',
                'journals'      => 'Diarios',
                'partners'      => 'Contactos',
                'entries'       => 'Asientos',
                'options'       => [
                    'due-date'       => 'Fecha de vencimiento',
                    'invoice-date'   => 'Fecha de factura',
                    'days-30'        => '30 días',
                    'days-60'        => '60 días',
                    'days-90'        => '90 días',
                    'posted-entries' => 'Asientos contabilizados',
                    'all-entries'    => 'Todos los asientos',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Antigüedad de cuentas por pagar',
                'group' => 'Informes de contactos',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'as-of'         => 'A fecha de',
                'based-on'      => 'Basado en',
                'period-length' => 'Duración del período (días)',
                'journals'      => 'Diarios',
                'partners'      => 'Contactos',
                'entries'       => 'Asientos',
                'options'       => [
                    'due-date'       => 'Fecha de vencimiento',
                    'invoice-date'   => 'Fecha de factura',
                    'days-30'        => '30 días',
                    'days-60'        => '60 días',
                    'days-90'        => '90 días',
                    'posted-entries' => 'Asientos contabilizados',
                    'all-entries'    => 'Todos los asientos',
                ],
            ],
        ],
        'multi-currency' => [
            'navigation'       => [
                'title' => 'Multimoneda',
                'group' => 'Informes de auditoría',
            ],
            'actions'          => [
                'export-excel' => 'Exportar a Excel',
            ],
            'filters'          => [
                'date-range'     => 'Periodo',
                'side'           => 'Documentos',
                'sales'          => 'Ventas (facturas y notas de crédito)',
                'purchases'      => 'Compras (facturas de proveedor y reembolsos)',
                'currency'       => 'Moneda',
                'all-currencies' => 'Todas las monedas',
            ],
            'note'             => 'Los importes en :base usan el tipo de cambio con que se contabilizó cada documento, salvo que una columna indique el tipo del día.',
            'empty'            => 'No hay facturas contabilizadas en este periodo.',
            'documents-count'  => '{1} 1 documento|[2,*] :count documentos',
            'company-currency' => 'Moneda de la empresa',
            'figures'          => [
                'invoiced' => 'Facturado',
                'received' => 'Cobrado',
                'open'     => 'Pendiente',
            ],
            'average-rate'     => 'Tipo medio: :rate',
            'sections'         => [
                'by-currency' => 'Por moneda',
                'exchange'    => 'Ganancias y pérdidas de cambio contabilizadas',
                'documents'   => 'Documentos',
            ],
            'columns'          => [
                'currency'      => 'Moneda',
                'invoiced'      => 'Facturado',
                'average-rate'  => 'Tipo medio',
                'invoiced-base' => 'Facturado en :base',
                'open'          => 'Pendiente',
                'open-base'     => 'Pendiente en :base (contabilizado)',
                'open-today'    => 'Pendiente en :base (hoy)',
                'difference'    => 'Diferencia',
                'number'        => 'Número',
                'date'          => 'Fecha',
                'contact'       => 'Contacto',
                'amount'        => 'Importe',
                'rate'          => 'Tipo',
                'base-amount'   => 'En :base',
                'status'        => 'Estado',
            ],
            'totals'           => [
                'total' => 'Total en :base',
            ],
            'today-note'       => 'El valor de hoy usa el último tipo registrado. Una moneda sin tipo muestra — y queda fuera de ese total.',
            'exchange'         => [
                'gains'  => 'Ganancias',
                'losses' => 'Pérdidas',
                'net'    => 'Neto',
            ],
            'documents-limit'  => 'Se muestran los últimos :count documentos. Los totales de arriba los cuentan todos.',
        ],
    ],
];
