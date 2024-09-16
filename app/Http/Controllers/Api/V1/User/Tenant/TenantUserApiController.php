<?php

namespace App\Http\Controllers\Api\V1\User\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Main\TenantMainResource;

class TenantUserApiController extends Controller
{
    public function index()
    {
        $tenants = auth()->user()->tenants()->latest()->paginate();

        return TenantMainResource::collection($tenants);
    }
}
