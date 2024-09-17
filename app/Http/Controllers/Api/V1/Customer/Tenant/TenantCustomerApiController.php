<?php

namespace App\Http\Controllers\Api\V1\Customer\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\TenantCustomerResource;
use App\Models\Tenant;

class TenantCustomerApiController extends Controller
{
    public function index()
    {
        $customerIds = auth()->user()->customers()->pluck('id');

        $tenants = Tenant::query()
            ->with(['customer', 'user'])
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->paginate();

        return TenantCustomerResource::collection($tenants);
    }
}
