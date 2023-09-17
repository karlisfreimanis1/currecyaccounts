<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiJsonRequest extends FormRequest
{
    /**
     * @param Validator $validator
     * @return mixed
     */
    public function failedValidation(Validator $validator): mixed
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors()
            ])
        );
    }
}
