<?php

namespace App\Http\Controllers\Api\V1\User\Meter;

use App\Http\Controllers\Controller;
use App\Http\Resources\Main\MeterReadingMainResource;
use App\Http\Resources\User\MeterUserResource;
use App\Models\Meter;

class MeterUserApiController extends Controller
{
    public function index()
    {
        $tenantIds = auth()->user()->tenants()->pluck('id');

        $meters = Meter::query()
            ->with(['utilityType', 'customer'])
            ->whereIn('tenant_id', $tenantIds)
            ->latest()
            ->paginate();

        return MeterUserResource::collection($meters);
    }

    public function getMeterReadings(Meter $meter)
    {
        $meter->load('tenant.user');

        abort_if($meter->tenant->user->id !== auth()->id(), 404, 'Meter not found');

        $meterReadings = $meter->meterReadings()->latest()->paginate();

        return MeterReadingMainResource::collection($meterReadings);
    }
}
