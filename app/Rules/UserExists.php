<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserExists implements ValidationRule
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isUserFound = User::where('id', $value)->get()->isEmpty();
        if ($isUserFound) {
            $fail('User not found id:' . $value);
        }
    }
}
