<?php

namespace App\Http\Controllers\Api\V1\Customer\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Main\CustomerMainResource;

class CustomerCustomerApiController extends Controller
{
    public function index()
    {
        $customers = auth()->user()->customers()->latest()->paginate();

        return CustomerMainResource::collection($customers);
    }
}
