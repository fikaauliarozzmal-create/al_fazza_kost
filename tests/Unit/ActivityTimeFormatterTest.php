<?php

namespace Tests\Unit;

use App\Services\ActivityTimeFormatter;
use Carbon\Carbon;
use Tests\TestCase;

class ActivityTimeFormatterTest extends TestCase
{
    public function test_it_formats_activity_times_in_jakarta_time(): void
    {
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setTestNow(Carbon::create(2026, 9, 20, 8, 32, 15, 'Asia/Jakarta'));

        $formatter = app(ActivityTimeFormatter::class);

        $this->assertSame('Baru saja', $formatter->format(now())['time_label']);
        $this->assertSame('5 detik yang lalu', $formatter->format(now()->subSeconds(5))['time_label']);
        $this->assertSame('30 detik yang lalu', $formatter->format(now()->subSeconds(30))['time_label']);
        $this->assertSame('1 menit yang lalu', $formatter->format(now()->subMinute())['time_label']);
        $this->assertSame('5 menit yang lalu', $formatter->format(now()->subMinutes(5))['time_label']);
        $this->assertSame('59 menit yang lalu', $formatter->format(now()->subMinutes(59))['time_label']);
        $this->assertSame('1 jam yang lalu', $formatter->format(now()->subHour())['time_label']);
        $this->assertSame('2 jam yang lalu', $formatter->format(now()->subHours(2))['time_label']);
        $this->assertSame('1 hari yang lalu', $formatter->format(now()->subDay())['time_label']);
        $this->assertSame('7 hari yang lalu', $formatter->format(now()->subDays(7))['time_label']);
        $this->assertSame('1 bulan yang lalu', $formatter->format(now()->subMonth())['time_label']);
        $this->assertSame('1 tahun yang lalu', $formatter->format(now()->subYear())['time_label']);
        $this->assertSame('20 September 2026, 08:32:15 WIB', $formatter->format(now())['time_full_label']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
