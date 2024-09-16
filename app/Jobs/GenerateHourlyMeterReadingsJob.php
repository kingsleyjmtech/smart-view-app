<?php

namespace App\Jobs;

use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateHourlyMeterReadingsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Meter $meter) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $readingValue = mt_rand(0, 100);

        MeterReading::query()->create([
            'meter_id' => $this->meter->id,
            'value' => $readingValue,
            'reading_date' => now(),
            'source' => 'Generated',
        ]);
    }
}
