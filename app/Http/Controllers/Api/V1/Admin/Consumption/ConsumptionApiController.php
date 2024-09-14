<?php

namespace App\Http\Controllers\Api\V1\Admin\Consumption;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Consumption\StoreConsumptionRequest;
use App\Http\Requests\Admin\Consumption\UpdateConsumptionRequest;
use App\Http\Resources\Admin\Consumption\ConsumptionResource;
use App\Models\Consumption;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ConsumptionApiController extends Controller
{
    public function index()
    {
        abort_if(
            ! auth()->user()->hasPermission('consumption_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return ConsumptionResource::collection(Consumption::query()->latest()->paginate());
    }

    public function store(StoreConsumptionRequest $request)
    {
        $consumption = Consumption::query()->create($request->validated());

        return (new ConsumptionResource($consumption))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Consumption $consumption)
    {
        abort_if(
            ! auth()->user()->hasPermission('consumption_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new ConsumptionResource($consumption);
    }

    public function update(UpdateConsumptionRequest $request, Consumption $consumption)
    {
        $consumption->update($request->validated());

        return (new ConsumptionResource($consumption))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(Consumption $consumption)
    {
        abort_if(
            ! auth()->user()->hasPermission('consumption_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $consumption->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
