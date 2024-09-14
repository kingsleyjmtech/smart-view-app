<?php

namespace App\Http\Controllers\Api\V1\Admin\UtilityType;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UtilityType\StoreUtilityTypeRequest;
use App\Http\Requests\Admin\UtilityType\UpdateUtilityTypeRequest;
use App\Http\Resources\Admin\UtilityType\UtilityTypeResource;
use App\Models\UtilityType;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UtilityTypeApiController extends Controller
{
    public function index()
    {
        abort_if(
            !auth()->user()->hasPermission('utility_type_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return UtilityTypeResource::collection(UtilityType::query()->latest()->paginate());
    }

    public function store(StoreUtilityTypeRequest $request)
    {
        $utilityType = UtilityType::query()->create($request->validated());

        return (new UtilityTypeResource($utilityType))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(UtilityType $utilityType)
    {
        abort_if(
            !auth()->user()->hasPermission('utility_type_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new UtilityTypeResource($utilityType);
    }

    public function update(UpdateUtilityTypeRequest $request, UtilityType $utilityType)
    {
        $utilityType->update($request->validated());

        return (new UtilityTypeResource($utilityType))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(UtilityType $utilityType)
    {
        abort_if(
            !auth()->user()->hasPermission('utility_type_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $utilityType->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}