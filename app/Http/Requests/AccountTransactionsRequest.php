<?php

namespace App\Http\Requests;

use App\Rules\AccountExists;

class AccountTransactionsRequest extends ApiJsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "accountId" => [
                'required',
                'integer',
                new AccountExists
            ],
            'limit' => [
                'integer',
                'nullable',
            ],
            'offset' => [
                'integer',
                'nullable',
            ]
        ];
    }
}
