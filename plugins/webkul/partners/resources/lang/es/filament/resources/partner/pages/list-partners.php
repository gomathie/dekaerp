<?php

return [
    'title' => 'Contactos',

    'header-actions' => [
        'create' => [
            'label' => 'Crear contacto',
        ],

        'import' => [
            'label' => 'Importar desde Excel',

            'modal' => [
                'heading'                 => 'Importar contactos desde una hoja de cálculo',
                'description'             => 'Descargue el archivo de ejemplo, rellene un contacto por fila y súbalo aquí. Hasta :max contactos por carga.',
                'submit'                  => 'Importar contactos',
                'download-template-csv'   => 'Descargar ejemplo (CSV)',
                'download-template-excel' => 'Descargar ejemplo (Excel)',

                'file' => [
                    'label'  => 'Archivo completado',
                    'helper' => 'Excel (.xlsx, .xls) o CSV. Mantenga la fila de encabezados tal cual. Columna obligatoria: :columns. Las filas con algún problema se informan por número de fila y el resto se importa igualmente.',
                ],
            ],

            'notifications' => [
                'imported'         => ':count contactos importados',
                'skipped'          => 'Se omitieron :count filas:',
                'nothing-imported' => 'No se importó ningún contacto',
                'more-errors'      => '... y :count filas más.',
                'unreadable'       => 'No se pudo leer ese archivo. Suba un archivo de Excel o CSV.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'Particulares',
        'companies'   => 'Empresas',
        'employees'   => 'Empleados',
        'customers'   => 'Clientes',
        'vendors'     => 'Proveedores',
        'archived'    => 'Archivados',
    ],
];
