<?php

/**
 * Example usage of the ZKTeco Biometric Laravel Package
 *
 * This file demonstrates how to use the package in your Laravel application.
 */

use AhidTechnologies\ZKTecoBiometric\Models\BiometricDevice;
use AhidTechnologies\ZKTecoBiometric\Models\BiometricEmployee;
use AhidTechnologies\ZKTecoBiometric\Models\BiometricCommand;
use AhidTechnologies\ZKTecoBiometric\Models\BiometricAttendance;
use AhidTechnologies\ZKTecoBiometric\ZKTecoBiometric;

// Example 1: Creating a new device
$device = BiometricDevice::create([
    'device_name' => 'Main Entrance Device',
    'serial_number' => 'BJHQ203160001',
    'device_ip' => '192.168.1.100',
    'status' => 'pending'
]);

// Example 2: Creating a biometric employee
$employee = BiometricEmployee::create([
    'biometric_employee_id' => '001',
    'user_id' => 1, // Your application's user ID
    'has_fingerprint' => false,
    'card_number' => null
]);

// Example 3: Using the ZKTeco facade/service
$zkteco = app(ZKTecoBiometric::class);

// Create a user on the device
$command = $zkteco->createUserCommand(
    'BJHQ203160001', // device serial
    '001',           // employee pin
    'John Doe',      // employee name
    1                // user ID (optional)
);

// Example 4: Getting attendance records
$attendances = $zkteco->getAttendance(
    'BJHQ203160001',                    // device serial (optional)
    '001',                              // employee ID (optional)
    new DateTime('2024-01-01'),         // from date (optional)
    new DateTime('2024-01-31')          // to date (optional)
);

// Example 5: Getting device status
$devices = $zkteco->getDevices();
foreach ($devices as $device) {
    echo "Device: {$device->device_name} - Status: {$device->status}\n";
}

// Example 6: Manually processing attendance (if needed)
$attendanceProcessor = app(\AhidTechnologies\ZKTecoBiometric\Services\AttendanceProcessor::class);

// This would typically be called automatically by the device endpoints
// but you can use it manually if needed
$rows = [
    "001\t2024-01-15 09:00:00\t0", // Employee 001, clock in
    "001\t2024-01-15 17:30:00\t1", // Employee 001, clock out
];

// $attendanceProcessor->processAttendanceRows($rows, $device, $request);

// Example 7: Querying specific attendance data
$todayAttendance = BiometricAttendance::whereDate('timestamp', today())
    ->where('device_serial_number', 'BJHQ203160001')
    ->get();

// Example 8: Getting pending commands for a device
$pendingCommands = $zkteco->getPendingCommands('BJHQ203160001');

echo "Found " . count($pendingCommands) . " pending commands for device\n";

// Example 9: Deleting a user from device
$deleteCommand = $zkteco->deleteUserCommand('BJHQ203160001', '001');

// Example 10: Custom attendance processing (override the service)
class CustomAttendanceProcessor extends \AhidTechnologies\ZKTecoBiometric\Services\AttendanceProcessor
{
    protected function findUserIdByEmployeeId(string $employeeId): ?int
    {
        // Custom logic to find user by employee ID
        // For example, if you have an EmployeeDetails model:
        // $employee = EmployeeDetails::where('employee_id', $employeeId)->first();
        // return $employee?->user_id;

        return null;
    }

    protected function processApplicationAttendance($user, string $timestamp): void
    {
        // Custom attendance processing logic
        // Override this method to implement your own attendance system integration

        if (!$user) {
            return;
        }

        // Your custom logic here
        \Log::info("Processing attendance for user {$user->id} at {$timestamp}");
    }
}

// Bind your custom processor
// app()->bind(
//     \AhidTechnologies\ZKTecoBiometric\Services\AttendanceProcessor::class,
//     CustomAttendanceProcessor::class
// );