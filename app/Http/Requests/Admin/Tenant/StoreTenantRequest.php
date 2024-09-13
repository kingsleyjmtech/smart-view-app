<?php

namespace App\Http\Requests\Admin\Tenant;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(
            !auth()->user()->hasPermission('tenant_create'),
            response()->json(
                ['message' => 'This action is unauthorized.'],
                ResponseAlias::HTTP_FORBIDDEN
            ),
        );

        return true;
     }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'integer',
                'exists:customers,id',
                'required'
            ],
            'user_id' => [
                'integer',
                'exists:users,id',
                'nullable'
            ],
            'uuid' => [
                'nullable',
                'string',
                'max:255'
            ]
        ];
    }
}