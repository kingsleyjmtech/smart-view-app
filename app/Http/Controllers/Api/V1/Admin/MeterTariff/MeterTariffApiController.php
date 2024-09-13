<?php

namespace App\Http\Controllers\Api\V1\Admin\MeterTariff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MeterTariff\StoreMeterTariffRequest;
use App\Http\Requests\Admin\MeterTariff\UpdateMeterTariffRequest;
use App\Http\Resources\Admin\MeterTariff\MeterTariffResource;
use App\Models\MeterTariff;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MeterTariffApiController extends Controller
{
    public function index()
    {
        abort_if(
            !auth()->user()->hasPermission('meter_tariff_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return MeterTariffResource::collection(MeterTariff::query()->latest()->paginate());
    }

    public function store(StoreMeterTariffRequest $request)
    {
        $meterTariff = MeterTariff::query()->create($request->validated());

        return (new MeterTariffResource($meterTariff))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(MeterTariff $meterTariff)
    {
        abort_if(
            !auth()->user()->hasPermission('meter_tariff_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new MeterTariffResource($meterTariff);
    }

    public function update(UpdateMeterTariffRequest $request, MeterTariff $meterTariff)
    {
        $meterTariff->update($request->validated());

        return (new MeterTariffResource($meterTariff))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(MeterTariff $meterTariff)
    {
        abort_if(
            !auth()->user()->hasPermission('meter_tariff_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $meterTariff->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}