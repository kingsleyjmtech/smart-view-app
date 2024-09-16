<?php

namespace App\Http\Resources\Main;

use App\Traits\Traits\Shared\FormatsTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterMainResource extends JsonResource
{
    use FormatsTime;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'customer_id' => $this->customer_id,
            'utility_type_id' => $this->utility_type_id,
            'code' => $this->code,
            'location' => $this->location,
            'installation_date' => $this->installation_date,
            'status' => $this->status,
            'created_at' => $this->formatTime($this->created_at),
            'updated_at' => $this->formatTime($this->updated_at),
        ];
    }
}
