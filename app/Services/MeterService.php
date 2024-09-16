<?php

namespace App\Services;

use App\Jobs\GenerateHourlyMeterReadingsJob;
use App\Models\Meter;

class MeterService
{
    public function processHourlyReadings(): void
    {
        $activeMeters = Meter::query()
            ->where('status', 'Active')
            ->get();

        foreach ($activeMeters as $meter) {
            GenerateHourlyMeterReadingsJob::dispatch($meter);
        }
    }
}
