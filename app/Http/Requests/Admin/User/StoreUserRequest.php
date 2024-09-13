<?php

namespace App\Http\Requests\Admin\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(
            !auth()->user()->hasPermission('user_create'),
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
            'roles' => [
                'required',
                'array'
            ],
            'roles.*.id' => [
                'integer',
                'exists:roles,id'
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255'
            ],
            'timezone' => [
                'nullable',
                'string',
                'max:255'
            ],
            'email_verified_at' => [
                'nullable'
            ],
            'password' => [
                'required',
                'string',
                'max:255'
            ],
            'remember_token' => [
                'nullable',
                'string',
                'max:255'
            ]
        ];
    }
}