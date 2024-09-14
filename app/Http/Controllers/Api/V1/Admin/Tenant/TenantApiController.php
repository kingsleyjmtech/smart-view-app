<?php

namespace App\Http\Controllers\Api\V1\Admin\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tenant\StoreTenantRequest;
use App\Http\Requests\Admin\Tenant\UpdateTenantRequest;
use App\Http\Resources\Admin\Tenant\TenantResource;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TenantApiController extends Controller
{
    public function index()
    {
        abort_if(
            ! auth()->user()->hasPermission('tenant_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return TenantResource::collection(Tenant::query()->latest()->paginate());
    }

    public function store(StoreTenantRequest $request)
    {
        $tenant = Tenant::query()->create($request->validated());

        return (new TenantResource($tenant))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Tenant $tenant)
    {
        abort_if(
            ! auth()->user()->hasPermission('tenant_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new TenantResource($tenant);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $tenant->update($request->validated());

        return (new TenantResource($tenant))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(Tenant $tenant)
    {
        abort_if(
            ! auth()->user()->hasPermission('tenant_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $tenant->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
