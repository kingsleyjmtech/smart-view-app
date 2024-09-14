<?php

namespace App\Http\Requests\Admin\MeterReading;

use App\Models\MeterReading;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UpdateMeterReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(            
            !auth()->user()->hasPermission('meter_reading_edit'),
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
            'reading_date' => [
                'required'
            ],
            'value' => [
                'required'
            ],
            'source' => [
                'required',
                'string',
                'max:255'
            ]
        ];
    }
}