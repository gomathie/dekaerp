<?php

return [
    'navigation' => [
        'label' => 'Ayuda',
    ],

    'title'      => 'Ayuda',
    'heading'    => 'Ayuda y recursos',
    'subheading' => 'Todo lo necesario para aprovechar al máximo :app.',

    'guides' => [
        'group' => 'Guías',

        'superadmin' => [
            'title'       => 'Guía del superadministrador',
            'description' => 'Administrar la propia instalación de :app: módulos, empresas, roles de administrador, actualizaciones y mantenimiento.',
            'button'      => 'Abrir la guía del superadministrador',
        ],

        'admin' => [
            'title'       => 'Guía del administrador',
            'description' => 'Configurar :app para la empresa, los equipos, los módulos y los controles antes de comenzar el trabajo diario.',
            'button'      => 'Lista de configuración del administrador',
            'sections'    => [
                [
                    'title' => 'Configurar la empresa',
                    'items' => [
                        '0' => 'Crear o revisar empresas, almacenes, monedas, impuestos y ajustes fiscales.',
                        '1' => 'Asignar los usuarios a las empresas a las que pueden acceder y comprobar el selector de empresa activa.',
                        '2' => 'Configurar secuencias, condiciones de pago, productos, unidades de medida y plantillas de documentos antes de empezar a operar.',
                    ],
                ],
                [
                    'title' => 'Controlar el acceso',
                    'items' => [
                        '0' => 'Crear roles para los equipos de finanzas, ventas, inventario, RR. HH., proyectos y dirección.',
                        '1' => 'Conceder permisos de módulo solo a los usuarios que los necesitan.',
                        '2' => 'Revisar campos personalizados, importaciones, exportaciones y tokens de API antes de dar acceso amplio.',
                    ],
                ],
                [
                    'title' => 'Operar y mantener',
                    'items' => [
                        '0' => 'Instalar solo los módulos que usa la empresa y ocultar o quitar los demás.',
                        '1' => 'Supervisar conversaciones, adjuntos, aprobaciones y propietarios de registros durante la implantación.',
                        '2' => 'Mantener los ajustes de correo, PDF, copias de seguridad y actualizaciones acordes con la política de despliegue.',
                    ],
                ],
            ],
        ],

        'user' => [
            'title'          => 'Guía del usuario',
            'description'    => 'Usar la guía integrada con los recorridos de DEKA y las páginas de referencia de cada módulo para el trabajo diario en :app.',
            'button'         => 'Abrir la guía del usuario',
            'full_heading'   => 'Guía completa del usuario',
            'count'          => '{0} Ninguna página de guía|{1} 1 página de guía|[2,*] :count páginas de guía',
            'category_count' => '{0} Ninguna página|{1} 1 página|[2,*] :count páginas',
            'callouts'       => [
                'tip'       => 'Consejo',
                'note'      => 'Nota',
                'important' => 'Importante',
            ],
            'sections'    => [
                [
                    'title' => 'Primeros pasos',
                    'items' => [
                        '0' => 'Visión general de la plataforma y navegación',
                        '1' => 'Configuración multiempresa y selector de empresa',
                    ],
                ],
                [
                    'title' => 'Procesos principales',
                    'items' => [
                        '0' => 'Vender: ventas y facturación',
                        '1' => 'Comprar: compras y contabilidad',
                        '2' => 'Fabricar y mover: inventario y producción',
                        '3' => 'Gestionar: proyectos, RR. HH. y mantenimiento',
                        '4' => 'Plataforma, API y alojamiento propio',
                    ],
                ],
                [
                    'title' => 'Referencia de módulos: 12 módulos / 69 páginas',
                    'items' => [
                        '0' => 'Facturas, Ventas, Compras, Inventario, Fabricación, Mantenimiento',
                        '1' => 'Contactos, Proyectos, Empleados, Contratación, Ausencias',
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'group' => 'Servicios',

        'cloud' => [
            'title'       => 'Alojamiento en la nube',
            'description' => 'Alojamiento en la nube gestionado y rentable: desplegar el ERP en minutos, totalmente optimizado, seguro y escalable.',
            'button'      => 'Ver precios',
        ],

        'support' => [
            'title'       => 'Soporte y mantenimiento',
            'description' => 'Soporte técnico dedicado y planes de mantenimiento continuo para mantener el ERP seguro, actualizado y funcionando sin problemas.',
            'button'      => 'Ver detalles',
        ],

        'paid' => [
            'title'       => 'Servicios de pago',
            'description' => 'Ayuda experta para el desarrollo de módulos, personalización, migración de datos, actualizaciones de versión e integraciones a medida.',
            'button'      => 'Comenzar',
        ],
    ],

    'resources' => [
        'group' => 'Recursos y documentación',

        'extensions' => [
            'title'       => 'Módulos',
            'description' => 'Explorar complementos oficiales y de la comunidad para ampliar :app con nuevos módulos y funciones.',
            'button'      => 'Explorar módulos',
        ],

        'docs' => [
            'title'       => 'Documentación para desarrolladores',
            'description' => 'Guías y referencias para desarrolladores que ayudan a crear plugins, personalizar módulos y ampliar la plataforma.',
            'button'      => 'Leer documentación',
        ],

        'guide' => [
            'title'       => 'Guía del usuario',
            'description' => 'Guías paso a paso que abarcan la instalación, la configuración y el uso diario de cada módulo del ERP.',
            'button'      => 'Abrir guía',
        ],

        'website' => [
            'title'       => 'Sitio del producto',
            'description' => 'Descripción de los módulos, notas de versión y todo lo que :app puede hacer, en el sitio web del producto.',
            'button'      => 'Visitar sitio web',
        ],
    ],

    'empty' => 'Todavía no se han configurado los enlaces de ayuda para esta instalación de :app. Póngase en contacto con el administrador del sistema.',

    'contact' => [
        'title'       => '¿Aún se necesita ayuda?',
        'description' => 'Hablar con nuestro equipo sobre alojamiento, implementación, desarrollo personalizado o cualquier otra cosa.',
        'live_chat'   => 'Chat en vivo',
        'button'      => 'Contáctanos',
    ],
];
