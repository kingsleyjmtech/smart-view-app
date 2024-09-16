<?php

namespace App\Http\Resources\Customer;

use App\Http\Resources\Main\CustomerMainResource;
use App\Http\Resources\Main\UserMainResource;
use App\Http\Resources\Main\UtilityTypeMainResource;
use App\Traits\Traits\Shared\FormatsTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantCustomerResource extends JsonResource
{
    use FormatsTime;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
            'uuid' => $this->uuid,
            'status' => $this->status,
            'created_at' => $this->formatTime($this->created_at),
            'updated_at' => $this->formatTime($this->updated_at),
            'customer' => new CustomerMainResource($this->whenLoaded('customer')),
            'user' => new UserMainResource($this->whenLoaded('user')),
        ];
    }
}
