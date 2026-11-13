<?php

return [
    'navigation' => [
        'title' => 'Resumen',
    ],

    'title' => 'Resumen',

    'sections' => [
        'sales'     => 'Ventas',
        'purchases' => 'Compras',
        'invoices'  => 'Facturas y facturas de proveedor',
    ],

    'filters' => [
        'sections'       => 'Mostrar',
        'all-sections'   => 'Todas las secciones',
        'date-range'     => 'Rango de fechas',
        'product'        => 'Producto',
        'all-products'   => 'Todos los productos',
        'category'       => 'Categoría',
        'all-categories' => 'Todas las categorías',
    ],

    'headline' => [
        'heading'         => 'De un vistazo',
        'sales'           => 'Ventas',
        'purchases'       => 'Compras',
        'invoices'        => 'Facturas',
        'revenue'         => 'Ingresos',
        'orders'          => '{0} Sin pedidos|{1} 1 pedido|[2,*] :count pedidos',
        'open-quotations' => 'Presupuestos abiertos',
        'spend'           => 'Gasto en compras',
        'open-rfqs'       => 'Solicitudes abiertas',
        'to-collect'      => 'Por cobrar',
        'to-pay'          => 'Por pagar',
        'overdue'         => '{0} Ninguno vencido|{1} 1 vencido|[2,*] :count vencidos',
    ],

    'attention' => [
        'heading'          => 'Requiere atención',
        'empty'            => 'Todo en orden. Nada está esperando por usted.',
        'overdue-invoices' => '{1} 1 factura vencida|[2,*] :count facturas vencidas',
        'overdue-bills'    => '{1} 1 factura de proveedor vencida|[2,*] :count facturas de proveedor vencidas',
        'bills-due-soon'   => '{1} 1 factura de proveedor vence esta semana|[2,*] :count facturas de proveedor vencen esta semana',
        'old-quotations'   => '{1} 1 presupuesto espera más de 7 días|[2,*] :count presupuestos esperan más de 7 días',
        'rfqs-to-approve'  => '{1} 1 compra por aprobar|[2,*] :count compras por aprobar',
    ],

    'actions' => [
        'new-invoice'   => 'Nueva factura',
        'new-bill'      => 'Nueva factura de proveedor',
        'new-quotation' => 'Nuevo presupuesto',
        'new-rfq'       => 'Nueva solicitud de presupuesto',
    ],

    'cash' => [
        'heading'     => 'Entradas y salidas de dinero',
        'description' => 'Facturas cobradas y facturas de proveedor pagadas por día',
        'in'          => 'Entradas',
        'out'         => 'Salidas',
    ],

    'trend' => ':change frente al periodo anterior',

    'modules' => [
        'heading' => 'En toda la empresa',
        'open'    => 'Abrir',
    ],

    'board' => [
        'updated'     => 'Actualizado a las :time',
        'orders'      => 'Pedidos',
        'conversion'  => 'Presupuesto → pedido',
        'by-supplier' => 'Gasto por proveedor',
        'other'       => 'Otros',
        'paid'        => 'Pagadas',
        'open'        => 'Abiertas',
        'overdue'     => 'Vencidas',
        'show'        => 'Mostrar',
        'tab-overdue' => 'Vencidas',
        'tab-soon'    => 'Vencen esta semana',
        'tab-open'    => 'Todas abiertas',
        'nothing'     => 'Nada aquí.',
        'customer'    => 'Cliente',
        'due'         => 'Vencimiento',
        'no-due-date' => 'Sin vencimiento',
        'due-today'   => 'Vence hoy',
        'due-on'      => 'Vence el :date',
        'days-late'   => '{1} 1 día de retraso|[2,*] :count días de retraso',
    ],

    'cash-compare' => [
        'previous'    => 'Periodo anterior',
        'this-period' => 'Este periodo',
        'vs-previous' => 'Frente al periodo anterior',
    ],
];
