<?php

namespace AhidTechnologies\ZKTecoBiometric\Events;

use AhidTechnologies\ZKTecoBiometric\Models\BiometricAttendance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BiometricAttendanceRecorded
{
    use Dispatchable, SerializesModels;

    public BiometricAttendance $attendance;

    /**
     * Create a new event instance.
     */
    public function __construct(BiometricAttendance $attendance)
    {
        $this->attendance = $attendance;
    }
}
