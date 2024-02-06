<?php
return [
    'type' => env('SSO_TYPE', 'server'),
    'sso_server_url' => env('SSO_SERVER_URL'),
    'broker_name' => env('SSO_BROKER_NAME'),
    'broker_secret' => env('SSO_BROKER_SECRET'),
    'logging' => [
        'channel' => env('SSO_LOGGING_CHANNEL'),
    ],
    'ajax-login' => env('SSO_AJAX_LOGIN', false),
];
