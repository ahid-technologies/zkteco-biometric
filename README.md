# ZKTeco Biometric Integration for Laravel

A comprehensive Laravel package for integrating ZKTeco biometric devices with attendance management systems.

## Features

-   **Device Management**: Add, configure, and monitor ZKTeco biometric devices
-   **Real-time Communication**: Handle device handshakes, commands, and data synchronization
-   **Attendance Tracking**: Automatic attendance logging from biometric data
-   **Employee Management**: Sync employee fingerprints, cards, and photos with devices
-   **Multi-timezone Support**: Proper timezone handling for global deployments
-   **Command System**: Send commands to devices (create user, delete user, etc.)
-   **Comprehensive Logging**: Detailed logs for debugging and monitoring

## Requirements

-   PHP 8.0 or higher
-   Laravel 10.0 or higher
-   MySQL/PostgreSQL database

## Installation

Install the package via Composer:

```bash
composer require ahidtechnologies/zkteco-biometric
```

Publish and run the migrations:

```bash
php artisan vendor:publish --provider="AhidTechnologies\ZKTecoBiometric\ZKTecoBiometricServiceProvider" --tag="migrations"
php artisan migrate
```

Optionally, publish the configuration file:

```bash
php artisan vendor:publish --provider="AhidTechnologies\ZKTecoBiometric\ZKTecoBiometricServiceProvider" --tag="config"
```

## Configuration

The package will automatically register the required routes. The default configuration includes:

-   API endpoints for device communication
-   Timezone handling
-   Attendance processing settings

## Usage

### Device Setup

1. **Add a Device**:

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricDevice;

$device = BiometricDevice::create([
    'device_name' => 'Main Entrance',
    'serial_number' => 'BJHQ203160001',
    'device_ip' => '192.168.1.100'
]);
```

2. **Configure Device IP**: Point your ZKTeco device to your Laravel application's URL with the following endpoints:
    - **Handshake**: `GET /iclock/cdata`
    - **Attendance Data**: `POST /iclock/cdata`
    - **Commands**: `GET /iclock/getrequest`
    - **Command Results**: `POST /iclock/devicecmd`

### Employee Management

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricEmployee;

// Create biometric employee
$employee = BiometricEmployee::create([
    'biometric_employee_id' => '001',
    'user_id' => $user->id, // Your application's user ID
    'has_fingerprint' => true,
    'card_number' => '1234567890'
]);
```

### Sending Commands to Device

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricCommand;

// Create user on device
BiometricCommand::create([
    'device_serial_number' => 'BJHQ203160001',
    'command_id' => 'CREATEUSER-001',
    'command' => "C:CREATEUSER-001:DATA USER PIN=001\tName=John Doe\n",
    'employee_id' => '001',
    'user_id' => $user->id,
    'status' => 'pending'
]);
```

### Attendance Processing

The package automatically processes attendance data when received from devices. You can also manually process attendance:

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricAttendance;

// Get attendance records
$attendances = BiometricAttendance::where('user_id', $userId)
    ->whereDate('timestamp', today())
    ->get();
```

## API Endpoints

The package registers the following API routes:

-   `GET /iclock/cdata` - Device handshake
-   `POST /iclock/cdata` - Attendance data submission
-   `GET /iclock/getrequest` - Command polling
-   `POST /iclock/devicecmd` - Command execution results

## Models

### BiometricDevice

Manages ZKTeco device information and status.

### BiometricEmployee

Links application users with biometric device employee IDs.

### BiometricAttendance

Stores attendance records from biometric devices.

### BiometricCommand

Manages commands sent to devices.

## Events

The package dispatches events for key operations:

-   Device online/offline status changes
-   Attendance records created
-   Command execution results

## Timezone Support

The package properly handles timezone conversions between device time and application time, ensuring accurate attendance logging across different timezones.

## Testing

Run the package tests:

```bash
vendor/bin/phpunit
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For support, please contact [Ahid Technologies](https://ahidtechnologies.com) or create an issue in the repository.

## Authors

-   **Ahid Technologies** - [https://ahidtechnologies.com](https://ahidtechnologies.com)
