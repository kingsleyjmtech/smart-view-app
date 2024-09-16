<?php

namespace App\Http\Resources\Main;

use App\Traits\Traits\Shared\FormatsTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UtilityTypeMainResource extends JsonResource
{
    use FormatsTime;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->formatTime($this->created_at),
            'updated_at' => $this->formatTime($this->updated_at),
        ];
    }
}
