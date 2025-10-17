<?php

namespace AhidTechnologies\ZKTecoBiometric\Tests\Unit;

use AhidTechnologies\ZKTecoBiometric\Models\BiometricDevice;
use AhidTechnologies\ZKTecoBiometric\Models\BiometricEmployee;
use AhidTechnologies\ZKTecoBiometric\Models\BiometricAttendance;
use AhidTechnologies\ZKTecoBiometric\Models\BiometricCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function biometric_device_can_be_created()
    {
        $device = BiometricDevice::create([
            'device_name' => 'Test Device',
            'serial_number' => 'TEST123',
            'device_ip' => '192.168.1.100',
            'status' => 'online',
        ]);

        $this->assertInstanceOf(BiometricDevice::class, $device);
        $this->assertEquals('Test Device', $device->device_name);
        $this->assertEquals('TEST123', $device->serial_number);
        $this->assertTrue($device->isOnline());
    }

    /** @test */
    public function biometric_employee_can_be_created()
    {
        $employee = BiometricEmployee::create([
            'biometric_employee_id' => 'EMP001',
            'user_id' => 1,
            'has_fingerprint' => true,
            'fingerprint_id' => 'FP001',
        ]);

        $this->assertInstanceOf(BiometricEmployee::class, $employee);
        $this->assertEquals('EMP001', $employee->biometric_employee_id);
        $this->assertTrue($employee->has_fingerprint);
    }

    /** @test */
    public function biometric_attendance_can_be_created()
    {
        $device = BiometricDevice::create([
            'device_name' => 'Test Device',
            'serial_number' => 'TEST123',
            'device_ip' => '192.168.1.100',
            'status' => 'online',
        ]);

        $attendance = BiometricAttendance::create([
            'device_serial_number' => $device->serial_number,
            'employee_id' => 'EMP001',
            'timestamp' => now(),
            'status1' => 0, // Check-in
        ]);

        $this->assertInstanceOf(BiometricAttendance::class, $attendance);
        $this->assertEquals('EMP001', $attendance->employee_id);
        $this->assertTrue($attendance->isCheckIn());
    }

    /** @test */
    public function biometric_command_can_be_created()
    {
        $device = BiometricDevice::create([
            'device_name' => 'Test Device',
            'serial_number' => 'TEST123',
            'device_ip' => '192.168.1.100',
            'status' => 'online',
        ]);

        $command = BiometricCommand::create([
            'type' => 'user_creation',
            'device_serial_number' => $device->serial_number,
            'command_id' => 'CREATEUSER_EMP001',
            'command' => 'create user command',
            'employee_id' => 'EMP001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(BiometricCommand::class, $command);
        $this->assertEquals('user_creation', $command->type);
        $this->assertTrue($command->isPending());
    }

    /** @test */
    public function device_relationships_work_correctly()
    {
        $device = BiometricDevice::create([
            'device_name' => 'Test Device',
            'serial_number' => 'TEST123',
            'device_ip' => '192.168.1.100',
            'status' => 'online',
        ]);

        $employee = BiometricEmployee::create([
            'biometric_employee_id' => 'EMP001',
            'user_id' => 1,
        ]);

        $attendance = BiometricAttendance::create([
            'device_serial_number' => $device->serial_number,
            'employee_id' => 'EMP001',
            'timestamp' => now(),
            'status1' => 0,
        ]);

        $command = BiometricCommand::create([
            'type' => 'user_creation',
            'device_serial_number' => $device->serial_number,
            'command_id' => 'CREATEUSER_EMP001',
            'command' => 'create user command',
            'employee_id' => 'EMP001',
            'status' => 'pending',
        ]);

        // Test relationships
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $device->attendances);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $device->commands);
        $this->assertEquals($device->serial_number, $attendance->device->serial_number);
        $this->assertEquals($device->serial_number, $command->device->serial_number);
    }
}
