<?php

return [
    'tabs' => [
        'help'       => 'Ayuda',
        'developers' => 'Desarrolladores',
    ],

    'heading' => 'Referencia de la API',
    'intro'   => 'Conecte su propio software con los datos de su empresa. Cada solicitud actúa como un usuario, con un token, y solo ve lo que ese usuario puede ver.',
    'general' => 'General',
    'empty'   => 'Esta empresa todavía no tiene módulos con API. Iniciar y cerrar sesión funciona para todas las empresas.',
    'copy'    => 'Copiar',
    'copied'  => 'Copiado',

    'badges' => [
        'version'   => 'API de :name v1',
        'total'     => '{0} Ningún endpoint|{1} 1 endpoint|[2,*] :count endpoints',
        'in_module' => ':count en :module',
    ],

    'modules' => [
        'label'       => 'Módulos',
        'sales'       => 'Ventas',
        'purchases'   => 'Compras',
        'accounts'    => 'Contabilidad',
        'inventories' => 'Inventario',
        'products'    => 'Productos',
        'partners'    => 'Contactos',
        'projects'    => 'Proyectos',
        'support'     => 'Configuración',
    ],

    'search' => [
        'placeholder' => 'Buscar en :module',
        'empty'       => 'Ningún endpoint coincide.',
    ],

    'getting_started' => [
        'title'               => 'Primeros pasos',
        'base_url'            => 'URL base',
        'authentication'      => 'Autenticación',
        'authentication_help' => 'Envíe su token en la cabecera Authorization de cada solicitud.',
        'token'               => 'Obtener un token',
        'token_page'          => 'Emita uno en Configuración › Tokens de API, para un usuario creado para la integración.',
        'token_page_button'   => 'Abrir tokens de API',
        'token_login'         => 'O haga que la integración inicie sesión con POST /admin/api/v1/login (abajo).',
        'scopes'              => 'Alcances',
        'scopes_help'         => 'Un token read solo puede hacer solicitudes GET. Crear, cambiar o eliminar cualquier cosa necesita write.',
        'company'             => 'Qué empresa',
        'company_help'        => 'Envíe X-Company-Id con una de las empresas del usuario del token. Sin esa cabecera, las solicitudes van a su empresa predeterminada.',
        'company_current'     => 'La empresa en la que trabaja tiene el id :id.',
        'limits'              => 'Límites',
        'rate_limit'          => '{1} 1 solicitud por minuto y por token.|[2,*] :count solicitudes por minuto y por token.',
        'expiry'              => 'Los tokens caducan después de :time.',
        'no_expiry'           => 'Los tokens no caducan.',
        'format'              => 'Formato',
        'format_help'         => 'Envíe siempre esta cabecera, para que los errores lleguen como JSON y no como una página web.',
    ],

    'full_reference' => [
        'title'       => 'Referencia completa',
        'description' => 'Todos los módulos y endpoints, con una consola para probar solicitudes. Se abre en una pestaña nueva y requiere haber iniciado sesión.',
        'button'      => 'Abrir referencia completa',
    ],

    'headers' => [
        'authorization' => 'Bearer seguido de su token.',
        'company'       => 'El id de la empresa en la que trabajar.',
        'accept'        => 'application/json',
    ],

    'in' => [
        'path'   => 'ruta',
        'query'  => 'consulta',
        'body'   => 'cuerpo',
        'header' => 'cabecera',
    ],

    'endpoint' => [
        'parameters'    => 'Parámetros',
        'name'          => 'Nombre',
        'type'          => 'Tipo',
        'description'   => 'Descripción',
        'required'      => 'Obligatorio',
        'optional'      => 'Opcional',
        'samples'       => 'Ejemplos de solicitud',
        'response'      => 'Respuesta (:status)',
        'other_answers' => 'Otras respuestas:',
        'no_token'      => 'Sin token',
    ],

    'answers' => [
        '401' => '401 sin token, o con uno caducado',
        '403' => '403 el usuario o la empresa del token no puede hacer esto',
        '422' => '422 los datos enviados no son válidos, y errors nombra cada campo',
        '429' => '429 demasiadas solicitudes, espere un minuto',
    ],
];
