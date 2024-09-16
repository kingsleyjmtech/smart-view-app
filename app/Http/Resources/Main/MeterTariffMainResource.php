<?php

namespace App\Http\Resources\Main;

use App\Traits\Traits\Shared\FormatsTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterTariffMainResource extends JsonResource
{
    use FormatsTime;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'meter_id' => $this->meter_id,
            'tariff_id' => $this->tariff_id,
            'effective_from' => $this->effective_from,
            'effective_to' => $this->effective_to,
            'created_at' => $this->formatTime($this->created_at),
            'updated_at' => $this->formatTime($this->updated_at),
        ];
    }
}
