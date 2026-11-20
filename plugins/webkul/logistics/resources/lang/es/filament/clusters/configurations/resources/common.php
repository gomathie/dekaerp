<?php

return [
    'fields' => [
        'name'           => 'Nombre',
        'code'           => 'Código',
        'is-active'      => 'Activo',
        'company'        => 'Empresa',
        'all-companies'  => 'Compartido (todas las empresas)',
        'company-helper' => 'Déjelo vacío para compartirlo con todas las empresas. Solo los usuarios que ven todas las empresas pueden crear o cambiar entradas compartidas.',
    ],

    'columns' => [
        'name'          => 'Nombre',
        'code'          => 'Código',
        'is-active'     => 'Activo',
        'company'       => 'Empresa',
        'all-companies' => 'Todas las empresas',
    ],

    'filters' => [
        'is-active' => 'Activo',
        'company'   => 'Empresa',
    ],
];
