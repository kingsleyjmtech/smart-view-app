<?php

namespace App\Traits\Traits\Shared;

use Illuminate\Support\Carbon;

trait FormatsTime
{
    public function formatTime($timestamp, $format = 'Y-m-d H:i:s', $timezone = null): string
    {
        $timezone = $timezone ?? config('app.timezone');

        return Carbon::parse($timestamp)->timezone($timezone)->format($format);
    }
}
