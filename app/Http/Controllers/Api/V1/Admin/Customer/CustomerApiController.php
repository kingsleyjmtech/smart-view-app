<?php

namespace App\Http\Controllers\Api\V1\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Http\Resources\Admin\Customer\CustomerResource;
use App\Models\Customer;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CustomerApiController extends Controller
{
    public function index()
    {
        abort_if(
            ! auth()->user()->hasPermission('customer_access'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return CustomerResource::collection(Customer::query()->latest()->paginate());
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::query()->create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(Customer $customer)
    {
        abort_if(
            ! auth()->user()->hasPermission('customer_show'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function destroy(Customer $customer)
    {
        abort_if(
            ! auth()->user()->hasPermission('customer_delete'),
            ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $customer->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
