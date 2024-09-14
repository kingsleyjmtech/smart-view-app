<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(
            ! auth()->user()->hasPermission('permission_edit'),
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
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
