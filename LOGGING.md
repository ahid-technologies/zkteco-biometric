# ZKTeco Package Logging System

## Overview

The ZKTeco package now includes a comprehensive logging system that respects Laravel's debug configuration and provides detailed insights into device communication.

## Logging Components

### 1. HasLogging Trait

-   **Location**: `src/Traits/HasLogging.php`
-   **Purpose**: Provides consistent logging methods across all package classes
-   **Methods**:
    -   `logInfo()` - Information level logging
    -   `logError()` - Error level logging
    -   `logWarning()` - Warning level logging
    -   `logDebug()` - Debug level logging (only when APP_DEBUG=true)
    -   `logDatabaseOperation()` - Database operation logging
    -   `logApiRequest()` - API request logging
    -   `isLoggingEnabled()` - Checks if logging should be enabled

### 2. Logging Middleware

-   **Location**: `src/Http/Middleware/LogZKTecoRequests.php`
-   **Purpose**: Automatically logs all requests to ZKTeco endpoints
-   **Features**:
    -   Request details (method, IP, user agent, headers, query params)
    -   Response details (status code, content length, processing time)
    -   Header sanitization (removes sensitive information)
    -   Configurable logging levels

### 3. Controller Endpoint Logging

-   **Location**: `src/Http/Controllers/ZKTecoController.php`
-   **Purpose**: Logs specific endpoint hits and business logic
-   **Endpoints Logged**:
    -   `/iclock/cdata` - Attendance data uploads
    -   `/iclock/getrequest` - Device handshake and command retrieval
    -   `/iclock/devicecmd` - Command execution responses
    -   `/iclock/ping` - Device ping/heartbeat

### 4. Service Layer Logging

-   **Location**: `src/Services/AttendanceProcessor.php`
-   **Purpose**: Logs attendance data processing operations

### 5. Main Package Logging

-   **Location**: `src/ZKTecoBiometric.php`
-   **Purpose**: Logs core package operations (device/employee creation, commands)

## Configuration Options

All logging is controlled through the `zkteco-biometric.logging` configuration:

```php
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
```

## Debug Mode Integration

When `respect_app_debug` is `true` (default):

-   Logging only occurs when Laravel's `APP_DEBUG` is `true`
-   This prevents production log spam while maintaining debug capabilities
-   Can be overridden by setting `respect_app_debug` to `false`

## Log Levels

-   **INFO**: Device connections, command creation, successful operations
-   **ERROR**: Missing device serial numbers, device not found, failures
-   **DEBUG**: Detailed request/response data, processing times, headers
-   **WARNING**: Unusual conditions that don't prevent operation

## Example Log Entries

### Device Ping Request:

```
[2025-09-30 08:38:57] local.DEBUG: [ZKTeco] API request to /iclock/ping
{"method":"GET","ip":"192.168.1.100","user_agent":"ZKTeco Test Device","query_params":{"SN":"QWC5251100***"}}

[2025-09-30 08:38:57] local.DEBUG: [ZKTeco] Device ping received
{"device_sn":"QWC5251100***","device_ip":"192.168.1.100"}
```

### Command Creation:

```
[2025-09-30 08:32:29] local.INFO: [ZKTeco] Creating user command for device
{"device_serial":"QWC5251100***","command_id":"CREATEUSER-68db959d3dbab","pin":"9999","name":"Test User"}
```

### Attendance Data Processing:

```
[2025-09-30 08:40:04] local.INFO: [ZKTeco] Attendance data received
{"device_sn":"QWC5251100***","rows_count":5,"device_ip":"192.168.1.100"}
```

## Benefits

1. **Full Visibility**: Track all device interactions and system operations
2. **Debug-Aware**: Respects Laravel's debug mode to prevent production bloat
3. **Configurable**: Granular control over what gets logged
4. **Performance Monitoring**: Track request processing times
5. **Security**: Automatically sanitizes sensitive headers
6. **Troubleshooting**: Detailed error logging with context
7. **Compliance**: Audit trail of all device communications

## Environment Variables

Add these to your `.env` file to customize logging:

```env
# Enable/disable logging entirely
ZKTECO_LOGGING_ENABLED=true

# Respect Laravel's debug mode
ZKTECO_RESPECT_APP_DEBUG=true

# Log channel to use
ZKTECO_LOG_CHANNEL=daily

# Specific logging toggles
ZKTECO_LOG_API_REQUESTS=true
ZKTECO_LOG_ATTENDANCE_DATA=true
ZKTECO_LOG_DEVICE_COMMANDS=true
ZKTECO_LOG_DATABASE_OPERATIONS=false
ZKTECO_LOG_RESPONSE_DETAILS=true
ZKTECO_LOG_REQUEST_HEADERS=true
ZKTECO_LOG_PROCESSING_TIME=true
```
