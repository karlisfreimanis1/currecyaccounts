<?php

namespace App\Http\Requests;

use App\Rules\UserExists;

class UserAccountListRequest extends ApiJsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "userId" => [
                'required',
                'integer',
                new UserExists
            ]
        ];
    }
}
