# ZKTeco Device Configuration Guide

This guide explains how to configure your ZKTeco biometric devices to work with this Laravel package.

## Device Setup

### 1. Network Configuration

First, ensure your ZKTeco device is connected to the same network as your Laravel application:

1. Access the device's admin panel (usually via its IP address in a web browser)
2. Go to **Communication** > **Network Settings**
3. Configure the device's IP address, subnet mask, and gateway
4. Test connectivity by pinging the device from your server

### 2. Server Configuration

Configure the device to communicate with your Laravel application:

1. In the device admin panel, go to **Communication** > **Server Settings**
2. Set the following parameters:

    - **Server IP**: Your Laravel application's server IP address
    - **Server Port**: 80 (for HTTP) or 443 (for HTTPS)
    - **Device SN**: The device's serial number (should be unique)
    - **Upload Interval**: Set to 1 minute for real-time sync

### 3. Communication URLs

Configure the device to use the correct endpoints:

**Primary Server URLs:**

-   Handshake URL: `http://your-domain.com/iclock/cdata`
-   Upload URL: `http://your-domain.com/iclock/cdata`
-   Download URL: `http://your-domain.com/iclock/getrequest`
-   Command URL: `http://your-domain.com/iclock/devicecmd`

Replace `your-domain.com` with your actual domain or IP address.

### 4. Device Protocol Settings

Set the communication protocol:

1. Go to **Communication** > **Protocol Settings**
2. Select **TCP/IP** as the communication method
3. Enable **Real-time Upload**
4. Set **Upload Protocol** to **HTTP**

### 5. Time Settings

Configure timezone settings:

1. Go to **System** > **Date/Time**
2. Set the correct timezone for your location
3. Enable **Auto Sync Time** if needed

## Laravel Application Setup

### 1. Add Device to Database

In your Laravel application, create the device record:

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricDevice;

$device = BiometricDevice::create([
    'device_name' => 'Main Entrance',
    'serial_number' => 'BJHQ203160001', // Must match device SN
    'device_ip' => '192.168.1.100',
    'status' => 'pending'
]);
```

### 2. Configure Employees

Add employees to the biometric system:

```php
use AhidTechnologies\ZKTecoBiometric\Models\BiometricEmployee;

$employee = BiometricEmployee::create([
    'biometric_employee_id' => '001', // Pin number on device
    'user_id' => 1, // Your application's user ID
]);
```

### 3. Send User to Device

Create a user on the device:

```php
use AhidTechnologies\ZKTecoBiometric\ZKTecoBiometric;

$zkteco = app(ZKTecoBiometric::class);

$command = $zkteco->createUserCommand(
    'BJHQ203160001', // device serial
    '001',           // employee pin
    'John Doe',      // employee name
    1                // user ID
);
```

## Testing the Connection

### 1. Device Status Check

Monitor device status in your application:

```php
$device = BiometricDevice::where('serial_number', 'BJHQ203160001')->first();
echo "Device Status: " . $device->status;
echo "Last Online: " . $device->last_online;
```

### 2. Test Attendance

1. Register a fingerprint on the device for a test user
2. Use the fingerprint to mark attendance
3. Check if attendance data appears in your Laravel application:

```php
$attendances = BiometricAttendance::where('device_serial_number', 'BJHQ203160001')
    ->whereDate('timestamp', today())
    ->get();
```

## Troubleshooting

### Common Issues

**Device shows offline:**

-   Check network connectivity
-   Verify server URLs are correct
-   Ensure firewall allows connections

**No attendance data:**

-   Verify device is sending data to correct endpoints
-   Check Laravel logs for any errors
-   Ensure employee exists in biometric_employees table

**Commands not executing:**

-   Check if device is polling for commands
-   Verify command format is correct
-   Check device and application logs

### Debug Mode

Enable debugging by setting in your `.env`:

```env
ZKTECO_LOGGING_ENABLED=true
ZKTECO_LOG_ATTENDANCE_DATA=true
ZKTECO_LOG_DEVICE_COMMANDS=true
```

Check logs in `storage/logs/laravel.log` for detailed information.

## Device-Specific Notes

### ZKTeco F18/F19/F22 Series

-   Supports fingerprint and card authentication
-   Maximum 1000 users
-   Real-time data upload

### ZKTeco iClock Series

-   Enterprise-grade devices
-   Support for photos and multiple verification methods
-   Higher user capacity (3000+ users)

### ZKTeco SpeedFace Series

-   Face recognition capable
-   Advanced verification modes
-   Touch screen interface

## Security Considerations

1. **Network Security**: Use HTTPS for production deployments
2. **Device Access**: Restrict physical access to device admin panels
3. **User Management**: Regularly audit user access and remove inactive users
4. **Data Backup**: Regularly backup biometric data and settings

## Maintenance

1. **Regular Updates**: Keep device firmware updated
2. **Data Cleanup**: Periodically clean old attendance records
3. **Performance Monitoring**: Monitor device response times and connectivity
4. **Battery Backup**: Ensure UPS backup for critical access points
