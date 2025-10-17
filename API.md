# API Documentation

This document describes the API endpoints and methods available in the ZKTeco Biometric Laravel Package.

## Device Communication Endpoints

These endpoints are used by ZKTeco devices to communicate with your Laravel application. **Do not apply authentication middleware to these routes.**

### Device Handshake

**Endpoint:** `GET /iclock/cdata`

Handles initial device handshake and configuration.

**Parameters:**

-   `SN` (required): Device serial number

**Response:** Device configuration string

**Example Request:**

```
GET /iclock/cdata?SN=BJHQ203160001
```

### Attendance Data Upload

**Endpoint:** `POST /iclock/cdata`

Receives attendance data from devices.

**Parameters:**

-   `SN` (required): Device serial number

**Body:** Raw attendance data or biometric templates

**Response:** `OK` or error message

### Command Polling

**Endpoint:** `GET /iclock/getrequest`

Devices poll this endpoint for pending commands.

**Parameters:**

-   `SN` (required): Device serial number

**Response:** Command string or `OK` if no commands

### Command Results

**Endpoint:** `POST /iclock/devicecmd`

Receives command execution results from devices.

**Parameters:**

-   `SN` (required): Device serial number

**Body:** Command execution result

**Response:** `OK`

### Device Ping

**Endpoint:** `GET /iclock/ping`

Simple endpoint for device status checks.

**Parameters:**

-   `SN` (optional): Device serial number

**Response:** `OK`

## PHP API Methods

### ZKTecoBiometric Class

The main service class providing high-level methods.

#### Device Management

```php
// Create a new device
$device = $zkteco->createDevice([
    'device_name' => 'Main Entrance',
    'serial_number' => 'BJHQ203160001',
    'device_ip' => '192.168.1.100'
]);

// Get all devices
$devices = $zkteco->getDevices();

// Get device by serial number
$device = $zkteco->getDeviceBySerial('BJHQ203160001');
```

#### Employee Management

```php
// Create biometric employee
$employee = $zkteco->createEmployee([
    'biometric_employee_id' => '001',
    'user_id' => 1
]);

// Get employee by biometric ID
$employee = $zkteco->getEmployeeByBiometricId('001');
```

#### Command Management

```php
// Create user on device
$command = $zkteco->createUserCommand(
    'BJHQ203160001', // device serial
    '001',           // employee pin
    'John Doe',      // name
    1                // user ID (optional)
);

// Delete user from device
$command = $zkteco->deleteUserCommand('BJHQ203160001', '001');

// Send custom command
$command = $zkteco->sendCommand(
    'BJHQ203160001',     // device serial
    'CUSTOM-001',        // command ID
    'C:CUSTOM-001:...',  // command string
    '001',               // employee ID (optional)
    1                    // user ID (optional)
);

// Get pending commands
$commands = $zkteco->getPendingCommands('BJHQ203160001');
```

#### Attendance Queries

```php
// Get all attendance records
$all = $zkteco->getAttendance();

// Get attendance for specific device
$deviceAttendance = $zkteco->getAttendance('BJHQ203160001');

// Get attendance for specific employee
$employeeAttendance = $zkteco->getAttendance(null, '001');

// Get attendance for date range
$rangeAttendance = $zkteco->getAttendance(
    'BJHQ203160001',
    '001',
    new DateTime('2024-01-01'),
    new DateTime('2024-01-31')
);
```

### Model Classes

#### BiometricDevice

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricDevice;

// Create device
$device = BiometricDevice::create([
    'device_name' => 'Main Entrance',
    'serial_number' => 'BJHQ203160001',
    'device_ip' => '192.168.1.100',
    'status' => 'pending'
]);

// Update device status
$device->markOnline('192.168.1.100');
$device->markOffline();

// Check status
if ($device->isOnline()) {
    echo "Device is online";
}

// Get related data
$employees = $device->employees;
$attendances = $device->attendances;
$commands = $device->commands;
```

#### BiometricEmployee

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricEmployee;

// Create employee
$employee = BiometricEmployee::create([
    'biometric_employee_id' => '001',
    'user_id' => 1,
    'has_fingerprint' => true,
    'card_number' => '1234567890'
]);

// Get relationships
$user = $employee->user;
$attendances = $employee->attendances;
```

#### BiometricAttendance

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricAttendance;

// Query attendance
$attendance = BiometricAttendance::where('employee_id', '001')
    ->whereDate('timestamp', today())
    ->get();

// Filter by type
$clockIns = BiometricAttendance::clockIn()->get();
$clockOuts = BiometricAttendance::clockOut()->get();

// Check attendance type
if ($attendance->isClockIn()) {
    echo "This is a clock in record";
}

// Get relationships
$device = $attendance->device;
$user = $attendance->user;
$employee = $attendance->biometricEmployee;
```

#### BiometricCommand

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricCommand;

// Create command
$command = BiometricCommand::create([
    'device_serial_number' => 'BJHQ203160001',
    'command_id' => 'CREATEUSER-001',
    'command' => BiometricCommand::createUserCommand('001', 'John Doe'),
    'employee_id' => '001',
    'user_id' => 1,
    'status' => 'pending'
]);

// Query commands
$pending = BiometricCommand::pending()->get();
$executed = BiometricCommand::executed()->get();
$failed = BiometricCommand::failed()->get();

// Check status
if ($command->isPending()) {
    echo "Command is pending execution";
}

// Mark as sent
$command->markAsSent();

// Generate command strings
$createUser = BiometricCommand::createUserCommand('UNIQUE-ID', '001', 'John Doe');
$deleteUser = BiometricCommand::deleteUserCommand('UNIQUE-ID', '001');
$queryUser = BiometricCommand::queryUserCommand('UNIQUE-ID', '001');
```

### AttendanceProcessor Service

Custom attendance processing service that can be extended:

```php
use AhidTechnologies\ZKTecoBiometric\Services\AttendanceProcessor;

class CustomAttendanceProcessor extends AttendanceProcessor
{
    protected function findUserIdByEmployeeId(string $employeeId): ?int
    {
        // Custom logic to map employee ID to user ID
        return User::where('employee_id', $employeeId)->first()?->id;
    }

    protected function processApplicationAttendance($user, string $timestamp): void
    {
        // Custom attendance processing logic
        // Integrate with your existing attendance system
    }
}

// Register custom processor
app()->bind(AttendanceProcessor::class, CustomAttendanceProcessor::class);
```

## Events and Extensibility

### Custom Event Handling

You can listen for model events to add custom functionality:

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricAttendance;

// Listen for new attendance records
BiometricAttendance::created(function ($attendance) {
    // Send notification, update reports, etc.
    Log::info("New attendance: Employee {$attendance->employee_id}");
});
```

### Configuration Options

All configuration options are available in `config/zkteco-biometric.php`:

```php
return [
    'route_prefix' => 'api/biometric',
    'middleware' => [],
    'timezone' => 'UTC',
    'attendance' => [
        'auto_create_users' => true,
        'log_attendance' => true,
    ],
    'logging' => [
        'enabled' => true,
        'channel' => 'daily',
    ],
];
```

## Error Handling

The package includes comprehensive error handling:

```php
try {
    $device = $zkteco->createDevice($data);
} catch (\Exception $e) {
    Log::error('Failed to create device: ' . $e->getMessage());
}
```

All device communication errors are logged automatically when logging is enabled.

## Database Schema

### Tables Created

-   `biometric_devices`: Device information and status
-   `biometric_employees`: Employee biometric data mapping
-   `biometric_attendances`: Raw attendance data from devices
-   `biometric_commands`: Commands sent to devices

### Indexes

The package creates appropriate indexes for optimal query performance:

-   Device serial numbers
-   Employee IDs
-   Timestamp fields
-   Status fields
-   User relationships

## Performance Considerations

1. **Batch Processing**: Attendance data is processed in batches for efficiency
2. **Indexes**: All frequently queried fields are indexed
3. **Caching**: Consider caching frequently accessed device and employee data
4. **Cleanup**: Implement regular cleanup of old attendance records
5. **Monitoring**: Monitor device communication for performance issues
