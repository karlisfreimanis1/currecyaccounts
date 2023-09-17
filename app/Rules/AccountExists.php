<?php

namespace App\Rules;

use App\Models\Account;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AccountExists implements ValidationRule
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $account = Account::where('id', $value)->get()->first();
        if ($account->isEmpty()) {
            $fail('Account not found id:' . $value);
        }
    }
}
