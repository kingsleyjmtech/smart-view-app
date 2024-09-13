<?php

namespace App\Http\Controllers\Api\V1\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Resources\Admin\User\UserResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserApiController extends Controller
{
    public function index()
    {
        abort_if(
            !auth()->user()->hasPermission('user_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return UserResource::collection(User::query()->latest()->paginate());
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::query()->create($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(User $user)
    {
        abort_if(
            !auth()->user()->hasPermission('user_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        abort_if(
            !auth()->user()->hasPermission('user_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $user->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}