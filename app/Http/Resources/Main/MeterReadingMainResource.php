<?php

namespace App\Http\Resources\Main;

use App\Traits\Traits\Shared\FormatsTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterReadingMainResource extends JsonResource
{
    use FormatsTime;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'meter_id' => $this->meter_id,
            'reading_date' => $this->reading_date,
            'value' => $this->value,
            'source' => $this->source,
            'created_at' => $this->formatTime($this->created_at),
            'updated_at' => $this->formatTime($this->updated_at),
        ];
    }
}
