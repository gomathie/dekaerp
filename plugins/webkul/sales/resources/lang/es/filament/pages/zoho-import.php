<?php

return [
    'navigation' => 'Importar desde Zoho Books',
    'title'      => 'Importar desde Zoho Books',
    'intro'      => 'Traiga el historial de una empresa desde Zoho Books: elija la empresa, suba la exportación, revise cómo se emparejan sus cuentas, impuestos y cuentas de dinero, y luego importe. Los documentos se registran del más antiguo al más reciente con sus números de Zoho y la página compara Zoho con DEKA.',
    'empty'      => 'Todavía no hay importaciones. Empiece con «Subir una exportación de Zoho».',
    'unused'     => ':count cuentas de Zoho no las usa ningún documento y se omiten.',

    'propose' => [
        'label'       => 'Subir una exportación de Zoho',
        'description' => 'El archivo de exportación (JSON, hasta 10 MB) se comprueba y se empareja con la empresa. Aún no se registra nada.',
        'company'     => 'Empresa',
        'file'        => 'Exportación de Zoho Books (JSON)',
        'done'        => 'El emparejamiento está listo. Revíselo abajo y luego importe.',
    ],

    'propose-options' => [
        'numbering'      => 'Numeración de los documentos nuevos',
        'numbering-deka' => 'Serie anual de DEKA (los documentos importados conservan sus números de Zoho)',
        'numbering-zoho' => 'Continuar el contador de Zoho',
        'skip-samples'   => 'Omitir los contactos de demostración de Zoho («(Sample)») que ningún documento usa',
    ],

    'compare' => [
        'label'       => 'Comparar con una nueva exportación de Zoho',
        'description' => 'Para el periodo en paralelo: suba una exportación reciente de Zoho para compararla con lo que DEKA tiene ahora, incluido lo registrado directamente en DEKA. No se guarda nada.',
        'queued'      => 'La comparación ha comenzado. Esta página la sigue.',
    ],

    'download' => [
        'label' => 'Descargar la comparación (CSV)',
    ],

    'unmatched' => [
        'none' => 'Cada factura, pago y gasto está en ambos sistemas.',
    ],

    'modes' => [
        'import'  => 'Importación',
        'compare' => 'Comparación',
    ],

    'import' => [
        'label'       => 'Importar',
        'confirm'     => '¿Importar en :company?',
        'description' => 'Todo el historial se registra en esta empresa. No se puede deshacer desde aquí; una importación detenida continúa donde se quedó al iniciarla de nuevo.',
        'queued'      => 'La importación ha comenzado. Esta página la sigue.',
    ],

    'run' => [
        'heading'    => 'Importación en :company',
        'status'     => 'Estado',
        'started-by' => 'Iniciada por',
        'source'     => 'En la exportación',
        'working'    => 'Importando. Puede tardar varios minutos; la página se actualiza sola.',
    ],

    'statuses' => [
        'proposed' => 'Esperando su aprobación',
        'queued'   => 'En cola',
        'running'  => 'Importando',
        'finished' => 'Terminada',
        'failed'   => 'Detenida',
    ],

    'sections' => [
        'accounts'   => 'Cuentas',
        'taxes'      => 'Impuestos',
        'banks'      => 'Cuentas bancarias y de caja',
        'records'    => 'Productos, contactos y numeración',
        'result'     => 'Lo que se registró',
        'unmatched'  => 'Documentos que están en un solo sistema',
        'comparison' => 'Zoho comparado con DEKA',
    ],

    'columns' => [
        'zoho'       => 'Zoho',
        'uses'       => 'Usos',
        'action'     => 'Acción',
        'deka'       => 'DEKA',
        'section'    => 'Sección',
        'row'        => 'Fila',
        'difference' => 'Diferencia',
    ],

    'actions' => [
        'map'    => 'usar existente',
        'create' => 'crear',
        'unused' => 'sin uso',
    ],

    'records' => [
        'items'          => 'Artículos: :create por crear, :map emparejados con productos existentes.',
        'contacts'       => 'Contactos: :create por crear, :map emparejados con contactos existentes.',
        'numbering-deka' => 'Los nuevos :kind usan la serie anual de DEKA; los importados conservan sus números de Zoho.',
        'numbering'      => 'Los nuevos :kind continúan con :next.',
    ],

    'comparison' => [
        'all-ok'      => 'Todas las filas coinciden.',
        'differences' => ':count filas difieren.',
    ],

    'errors' => [
        'empty-file' => 'No se pudo leer el archivo subido.',
        'no-run'     => 'Primero suba una exportación.',
    ],
];
