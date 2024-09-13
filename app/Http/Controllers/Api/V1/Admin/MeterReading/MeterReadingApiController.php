<?php

namespace App\Http\Controllers\Api\V1\Admin\MeterReading;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MeterReading\StoreMeterReadingRequest;
use App\Http\Requests\Admin\MeterReading\UpdateMeterReadingRequest;
use App\Http\Resources\Admin\MeterReading\MeterReadingResource;
use App\Models\MeterReading;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MeterReadingApiController extends Controller
{
    public function index()
    {
        abort_if(
            !auth()->user()->hasPermission('meter_reading_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return MeterReadingResource::collection(MeterReading::query()->latest()->paginate());
    }

    public function store(StoreMeterReadingRequest $request)
    {
        $meterReading = MeterReading::query()->create($request->validated());

        return (new MeterReadingResource($meterReading))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(MeterReading $meterReading)
    {
        abort_if(
            !auth()->user()->hasPermission('meter_reading_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new MeterReadingResource($meterReading);
    }

    public function update(UpdateMeterReadingRequest $request, MeterReading $meterReading)
    {
        $meterReading->update($request->validated());

        return (new MeterReadingResource($meterReading))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(MeterReading $meterReading)
    {
        abort_if(
            !auth()->user()->hasPermission('meter_reading_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $meterReading->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}