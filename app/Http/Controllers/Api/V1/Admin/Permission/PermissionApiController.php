<?php

namespace App\Http\Controllers\Api\V1\Admin\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Permission\StorePermissionRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionRequest;
use App\Http\Resources\Admin\Permission\PermissionResource;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PermissionApiController extends Controller
{
    public function index()
    {
        abort_if(
            !auth()->user()->hasPermission('permission_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return PermissionResource::collection(Permission::query()->latest()->paginate());
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::query()->create($request->validated());

        return (new PermissionResource($permission))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Permission $permission)
    {
        abort_if(
            !auth()->user()->hasPermission('permission_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new PermissionResource($permission);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return (new PermissionResource($permission))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(Permission $permission)
    {
        abort_if(
            !auth()->user()->hasPermission('permission_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $permission->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}