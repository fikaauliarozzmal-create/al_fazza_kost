<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeInterface;

class ActivityTimeFormatter
{
    public function format(mixed $value): array
    {
        $timezone = config('app.timezone');
        $time = $value instanceof DateTimeInterface
            ? Carbon::instance($value)
            : Carbon::parse($value, $timezone);

        $time->setTimezone($timezone)->locale('id');
        $now = now($timezone);

        return [
            'time' => $time,
            'timestamp' => $time->getTimestamp(),
            'time_label' => $this->relative($time, $now),
            'time_full_label' => $time->translatedFormat('d F Y, H:i:s \\W\\I\\B'),
            'time_iso' => $time->toIso8601String(),
        ];
    }

    private function relative(Carbon $time, Carbon $now): string
    {
        if ($time->isFuture()) {
            return $time->diffForHumans($now, ['parts' => 1]);
        }

        $seconds = (int) $time->diffInSeconds($now);

        if ($seconds < 5) {
            return 'Baru saja';
        }

        if ($seconds < 60) {
            return $seconds . ' detik yang lalu';
        }

        $minutes = (int) $time->diffInMinutes($now);
        if ($minutes < 60) {
            return $minutes . ' menit yang lalu';
        }

        $hours = (int) $time->diffInHours($now);
        if ($hours < 24) {
            return $hours . ' jam yang lalu';
        }

        // Carbon menghitung bulan dan tahun berdasarkan kalender, bukan 30 hari tetap.
        $years = (int) $time->diffInYears($now);
        if ($years >= 1) {
            return $years . ' tahun yang lalu';
        }

        $months = (int) $time->diffInMonths($now);
        if ($months >= 1) {
            return $months . ' bulan yang lalu';
        }

        return (int) $time->diffInDays($now) . ' hari yang lalu';
    }
}
