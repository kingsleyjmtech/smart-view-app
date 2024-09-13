<?php

namespace App\Http\Requests\Admin\MeterTariff;

use App\Models\MeterTariff;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UpdateMeterTariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(            
            !auth()->user()->hasPermission('meter_tariff_edit'),
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
                'required'
            ],
            'tariff_id' => [
                'integer',
                'exists:tariffs,id',
                'required'
            ],
            'effective_from' => [
                'required'
            ],
            'effective_to' => [
                'nullable'
            ]
        ];
    }
}