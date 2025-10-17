<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes for ZKTeco device communication.
    |
    */
    'route_prefix' => env('ZKTECO_ROUTE_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Define middleware that should be applied to the ZKTeco routes.
    | Note: Authentication middleware should not be applied to device endpoints.
    | The LogZKTecoRequests middleware is automatically added when logging is enabled.
    |
    */
    'middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configure database table names and connections.
    |
    */
    'database' => [
        'connection' => env('ZKTECO_DB_CONNECTION', null),
        'tables' => [
            'devices' => 'biometric_devices',
            'employees' => 'biometric_employees',
            'attendances' => 'biometric_device_attendances',
            'commands' => 'biometric_commands',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Timezone Configuration
    |--------------------------------------------------------------------------
    |
    | Default timezone for device communication.
    |
    */
    'timezone' => env('ZKTECO_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Attendance Configuration
    |--------------------------------------------------------------------------
    |
    | Configure attendance processing settings.
    |
    */
    'attendance' => [
        'auto_create_users' => env('ZKTECO_AUTO_CREATE_USERS', true),
        'log_attendance' => env('ZKTECO_LOG_ATTENDANCE', true),
        'sync_to_application' => env('ZKTECO_SYNC_TO_APPLICATION', true),
        'employee_field' => env('ZKTECO_EMPLOYEE_FIELD', 'employee_code'), // Field name in User model
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Configure model classes for the package.
    |
    */
    'models' => [
        'attendance' => env('ZKTECO_ATTENDANCE_MODEL', 'App\Models\Attendance'),
        'user' => env('ZKTECO_USER_MODEL', 'App\Models\User'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Command Configuration
    |--------------------------------------------------------------------------
    |
    | Configure command execution settings.
    |
    */
    'commands' => [
        'timeout' => env('ZKTECO_COMMAND_TIMEOUT', 300), // 5 minutes
        'retry_attempts' => env('ZKTECO_COMMAND_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for device communication.
    | When 'respect_app_debug' is true, logging will only be enabled when
    | Laravel's APP_DEBUG is true. Otherwise, it uses the enabled setting.
    |
    */
    'logging' => [
        'enabled' => env('ZKTECO_LOGGING_ENABLED', true),
        'respect_app_debug' => env('ZKTECO_RESPECT_APP_DEBUG', true),
        'channel' => env('ZKTECO_LOG_CHANNEL', 'daily'),
        'log_attendance_data' => env('ZKTECO_LOG_ATTENDANCE_DATA', true),
        'log_device_commands' => env('ZKTECO_LOG_DEVICE_COMMANDS', true),
        'log_api_requests' => env('ZKTECO_LOG_API_REQUESTS', true),
        'log_database_operations' => env('ZKTECO_LOG_DATABASE_OPERATIONS', false),
        'log_response_details' => env('ZKTECO_LOG_RESPONSE_DETAILS', true),
        'log_request_headers' => env('ZKTECO_LOG_REQUEST_HEADERS', true),
        'log_processing_time' => env('ZKTECO_LOG_PROCESSING_TIME', true),
    ],
];
