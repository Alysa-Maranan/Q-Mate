<?php

namespace Tests\Unit;

use App\Support\TimeFormat;
use PHPUnit\Framework\TestCase;

class TimeFormatTest extends TestCase
{
    public function test_normalizes_ampm_and_24_hour_values_without_losing_time(): void
    {
        $this->assertSame('00:00', TimeFormat::normalize('12:00 AM'));
        $this->assertSame('12:00', TimeFormat::normalize('12:00 PM'));
        $this->assertSame('07:15', TimeFormat::normalize('7:15 am'));
        $this->assertSame('19:45', TimeFormat::normalize('7:45 PM'));
        $this->assertSame('13:30', TimeFormat::normalize('13:30'));
        $this->assertNull(TimeFormat::normalize('13:30 PM'));
        $this->assertNull(TimeFormat::normalize('25:00'));
    }

    public function test_displays_database_times_in_twelve_hour_format(): void
    {
        $this->assertSame('12:00 AM', TimeFormat::display('00:00:00'));
        $this->assertSame('12:00 PM', TimeFormat::display('12:00'));
        $this->assertSame('7:45 PM', TimeFormat::display('19:45:00'));
        $this->assertSame('', TimeFormat::display(null));
    }
}
