<?php

namespace App\Http\Controllers\Api\V1\Admin\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Http\Resources\Admin\Role\RoleResource;
use App\Models\Role;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RoleApiController extends Controller
{
    public function index()
    {
        abort_if(
            ! auth()->user()->hasPermission('role_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return RoleResource::collection(Role::query()->latest()->paginate());
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::query()->create($request->validated());

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Role $role)
    {
        abort_if(
            ! auth()->user()->hasPermission('role_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new RoleResource($role);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(Role $role)
    {
        abort_if(
            ! auth()->user()->hasPermission('role_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $role->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
