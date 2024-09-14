<?php

namespace App\Http\Requests\Admin\Meter;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StoreMeterRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(
            ! auth()->user()->hasPermission('meter_create'),
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
            'tenant_id' => [
                'integer',
                'exists:tenants,id',
                'required',
            ],
            'user_id' => [
                'integer',
                'exists:users,id',
                'nullable',
            ],
            'utility_type_id' => [
                'integer',
                'exists:utility_types,id',
                'required',
            ],
            'code' => [
                'required',
                'string',
                'max:255',
            ],
            'location' => [
                'required',
                'string',
                'max:255',
            ],
            'installation_date' => [
                'nullable',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
