<?php

return [
    'errors' => [
        'empty'           => 'El archivo no contiene filas.',
        'no-columns'      => 'No se encontró ninguna columna reconocida en la fila de encabezados. Columnas esperadas: :columns.',
        'missing-name'    => 'El archivo debe tener una columna «name».',
        'max-rows'        => 'Solo se importaron las primeras :max filas. Divida el archivo y suba el resto.',
        'row'             => 'Fila :row: :reason',
        'formula'         => 'Fila :row: el valor «:column» empieza por un carácter que una hoja de cálculo interpreta como fórmula. Elimínelo y vuelva a subir el archivo.',
        'duplicate-email' => 'Fila :row: ya existe un contacto con el correo :email.',
        'unknown-country' => 'Fila :row: ningún país coincide con «:country».',
        'unknown-state'   => 'Fila :row: ninguna provincia coincide con «:state».',
        'unknown-parent'  => 'Fila :row: ningún contacto existente coincide con la empresa «:parent».',
    ],
];
