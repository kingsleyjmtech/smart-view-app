<?php

namespace App\Http\Requests\Admin\Customer;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(            
            !auth()->user()->hasPermission('customer_edit'),
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
            'user_id' => [
                'integer',
                'exists:users,id',
                'required'
            ],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100'
            ]
        ];
    }
}