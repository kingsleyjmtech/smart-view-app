<?php

namespace App\Http\Controllers\Api\V1\Customer\Meter;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\MeterCustomerResource;
use App\Http\Resources\Main\MeterReadingMainResource;
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

    public function getMeterReadings(Meter $meter)
    {
        $meter->load('customer.user');

        abort_if($meter->customer->user->id !== auth()->id(), 404, 'Meter not found');

        $meterReadings = $meter->meterReadings()->latest()->paginate();

        return MeterReadingMainResource::collection($meterReadings);
    }
}
