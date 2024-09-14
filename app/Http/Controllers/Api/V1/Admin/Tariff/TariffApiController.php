<?php

namespace App\Http\Controllers\Api\V1\Admin\Tariff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tariff\StoreTariffRequest;
use App\Http\Requests\Admin\Tariff\UpdateTariffRequest;
use App\Http\Resources\Admin\Tariff\TariffResource;
use App\Models\Tariff;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TariffApiController extends Controller
{
    public function index()
    {
        abort_if(
            ! auth()->user()->hasPermission('tariff_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return TariffResource::collection(Tariff::query()->latest()->paginate());
    }

    public function store(StoreTariffRequest $request)
    {
        $tariff = Tariff::query()->create($request->validated());

        return (new TariffResource($tariff))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Tariff $tariff)
    {
        abort_if(
            ! auth()->user()->hasPermission('tariff_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new TariffResource($tariff);
    }

    public function update(UpdateTariffRequest $request, Tariff $tariff)
    {
        $tariff->update($request->validated());

        return (new TariffResource($tariff))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(Tariff $tariff)
    {
        abort_if(
            ! auth()->user()->hasPermission('tariff_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $tariff->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
