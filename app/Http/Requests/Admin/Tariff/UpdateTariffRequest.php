<?php

namespace App\Http\Requests\Admin\Tariff;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UpdateTariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(            
            !auth()->user()->hasPermission('tariff_edit'),
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
            'rate' => [
                'required'
            ],
            'description' => [
                'required',
                'string'
            ],
            'start_date' => [
                'required'
            ],
            'end_date' => [
                'nullable'
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