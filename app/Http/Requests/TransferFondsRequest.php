<?php

namespace App\Http\Requests;

use App\Rules\AccountExists;
use App\Rules\IsFondsAvailibleRule;

class TransferFondsRequest extends ApiJsonRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'accountIdFrom' => [
                'required',
                'integer',
                new AccountExists
            ],
            'accountIdTo' => [
                'required',
                'integer',
                new AccountExists
            ],
            'value' => [
                'required',
                'decimal:6',
                'min:0',
                'not_in:0'
            ],
        ];
    }
}
