<?php

namespace App\Http\Resources\Admin\Consumption;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsumptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
