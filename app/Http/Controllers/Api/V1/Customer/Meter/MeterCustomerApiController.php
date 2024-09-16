<?php

namespace App\Http\Controllers\Api\V1\Customer\Meter;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\MeterCustomerResource;
use App\Models\Meter;

class MeterCustomerApiController extends Controller
{
    public function index()
    {
        $customerIds = auth()->user()->customers()->pluck('id');

        $meters = Meter::query()
            ->with(['utilityType', 'customer', 'tenant.user'])
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->paginate();

        return MeterCustomerResource::collection($meters);
    }
}
