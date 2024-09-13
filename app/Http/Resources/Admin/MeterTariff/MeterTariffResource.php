<?php

namespace App\Http\Resources\Admin\MeterTariff;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterTariffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
