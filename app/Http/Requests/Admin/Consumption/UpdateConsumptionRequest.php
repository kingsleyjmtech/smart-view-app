<?php

namespace App\Http\Requests\Admin\Consumption;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UpdateConsumptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(
            ! auth()->user()->hasPermission('consumption_edit'),
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
            'meter_id' => [
                'integer',
                'exists:meters,id',
                'required',
            ],
            'aggregation_period' => [
                'nullable',
            ],
            'value' => [
                'required',
            ],
            'date' => [
                'required',
            ],
        ];
    }
}
