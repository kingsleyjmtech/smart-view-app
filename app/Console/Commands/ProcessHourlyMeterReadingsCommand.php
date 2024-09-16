<?php

namespace App\Console\Commands;

use App\Services\MeterService;
use Illuminate\Console\Command;

class ProcessHourlyMeterReadingsCommand extends Command
{
    protected $signature = 'meters:process-hourly-readings';

    protected $description = 'Processes hourly meter readings for all active meters.';

    public function handle(MeterService $meterService): void
    {
        $meterService->processHourlyReadings();
        $this->info('Hourly meter readings have been processed.');
    }
}
