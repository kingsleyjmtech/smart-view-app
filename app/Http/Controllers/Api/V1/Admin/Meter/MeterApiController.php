<?php

namespace App\Http\Controllers\Api\V1\Admin\Meter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Meter\StoreMeterRequest;
use App\Http\Requests\Admin\Meter\UpdateMeterRequest;
use App\Http\Resources\Admin\Meter\MeterResource;
use App\Models\Meter;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MeterApiController extends Controller
{
    public function index()
    {
        abort_if(
            !auth()->user()->hasPermission('meter_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return MeterResource::collection(Meter::query()->latest()->paginate());
    }

    public function store(StoreMeterRequest $request)
    {
        $meter = Meter::query()->create($request->validated());

        return (new MeterResource($meter))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Meter $meter)
    {
        abort_if(
            !auth()->user()->hasPermission('meter_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new MeterResource($meter);
    }

    public function update(UpdateMeterRequest $request, Meter $meter)
    {
        $meter->update($request->validated());

        return (new MeterResource($meter))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(Meter $meter)
    {
        abort_if(
            !auth()->user()->hasPermission('meter_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $meter->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}