<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app/private'),
            'serve'  => true,
            'throw'  => false,
        ],

        /*
         | Uploads are written through disk('public') in ~27 places across the
         | plugins. Rather than change those call sites, the disk itself is
         | switchable: 'local' for development, 'tenant-s3' in production,
         | which stores objects privately in S3 under companies/{company_id}/.
         | See App\Providers\TenantFilesystemServiceProvider.
         |
         | The container filesystem is replaced on every deploy, so 'local' is
         | only safe where storage/ is a persistent volume.
         */
        'public' => [
            'driver'     => env('FILESYSTEM_PUBLIC_DRIVER', 'local'),
            'root'       => env('FILESYSTEM_PUBLIC_DRIVER', 'local') === 'local'
                ? storage_path('app/public')
                : env('AWS_ROOT', ''),
            // With the local driver, public/storage is a symlink and files are
            // served directly. Object storage is private and has no fetchable
            // URL, so ->url() is pointed at a route that authorizes the request
            // first - see App\Http\Controllers\SecureStorageController. Every
            // Filament component calling ->url() picks this up automatically.
            'url'        => env('FILESYSTEM_PUBLIC_DRIVER', 'local') === 'local'
                ? env('APP_URL').'/storage'
                : env('APP_URL').'/secure-storage',
            'visibility' => env('FILESYSTEM_PUBLIC_DRIVER', 'local') === 'local'
                ? 'public'
                : 'private',
            'throw'      => false,

            // Used only when the driver is tenant-s3; ignored by the local driver.
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
