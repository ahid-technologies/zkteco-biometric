<?php

namespace AhidTechnologies\ZKTecoBiometric\Tests\Feature;

use AhidTechnologies\ZKTecoBiometric\Tests\TestCase;
use AhidTechnologies\ZKTecoBiometric\Models\BiometricDevice;

class BiometricDeviceTest extends TestCase
{
    /** @test */
    public function it_can_create_a_biometric_device()
    {
        $device = BiometricDevice::create([
            'device_name' => 'Test Device',
            'serial_number' => 'TEST001',
            'device_ip' => '192.168.1.100',
            'status' => 'pending'
        ]);

        $this->assertInstanceOf(BiometricDevice::class, $device);
        $this->assertEquals('Test Device', $device->device_name);
        $this->assertEquals('TEST001', $device->serial_number);
        $this->assertEquals('pending', $device->status);
    }

    /** @test */
    public function it_can_mark_device_as_online()
    {
        $device = BiometricDevice::create([
            'device_name' => 'Test Device',
            'serial_number' => 'TEST001',
            'device_ip' => '192.168.1.100',
            'status' => 'pending'
        ]);

        $device->markOnline('192.168.1.101');

        $this->assertEquals('online', $device->fresh()->status);
        $this->assertEquals('192.168.1.101', $device->fresh()->device_ip);
        $this->assertNotNull($device->fresh()->last_online);
    }

    /** @test */
    public function it_can_check_if_device_is_online()
    {
        $device = BiometricDevice::create([
            'device_name' => 'Test Device',
            'serial_number' => 'TEST001',
            'device_ip' => '192.168.1.100',
            'status' => 'online'
        ]);

        $this->assertTrue($device->isOnline());

        $device->markOffline();

        $this->assertFalse($device->fresh()->isOnline());
    }
}
