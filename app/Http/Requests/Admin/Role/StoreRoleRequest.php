<?php

namespace App\Http\Requests\Admin\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(
            !auth()->user()->hasPermission('role_create'),
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
            'permissions' => [
                'required',
                'array'
            ],
            'permissions.*.id' => [
                'integer',
                'exists:permissions,id'
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ]
        ];
    }
}