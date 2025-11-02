<?php

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Mama Mia Pizza API',
            ],

            'routes' => [
                'api' => 'api/documentation',
                'docs' => 'docs',
                'oauth2_callback' => 'api/oauth2-callback',
            ],

            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                'swagger_ui_assets_path' => env(
                    'L5_SWAGGER_UI_ASSETS_PATH',
                    'vendor/swagger-api/swagger-ui/dist/'
                ),

                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                // ✅ ГЛАВНОЕ: откуда СКАНИМ аннотации
                'annotations' => [
                    base_path('app/Http/Controllers'),
                    base_path('app/Swagger'),
                ],

                // ✅ обязательно для Generator.php (у тебя уже падало на "Undefined array key excludes")
                'excludes' => [
                    base_path('app/Http/Controllers/Controller.php'),
                ],
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],

        'paths' => [
            'docs' => storage_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'excludes' => [],
        ],

        'scanOptions' => [
            // ✅ пусть сканит все *.php
            'pattern' => '*.php',
            'exclude' => [],
            'open_api_spec_version' => env(
                'L5_SWAGGER_OPEN_API_SPEC_VERSION',
                \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION
            ),
        ],
    ],
];
