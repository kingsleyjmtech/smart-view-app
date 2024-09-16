<?php

namespace App\Http\Resources\Main;

use App\Traits\Traits\Shared\FormatsTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TariffMainResource extends JsonResource
{
    use FormatsTime;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rate' => $this->rate,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'name' => $this->name,
            'created_at' => $this->formatTime($this->created_at),
            'updated_at' => $this->formatTime($this->updated_at),
        ];
    }
}
