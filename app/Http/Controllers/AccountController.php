<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAccountListRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    /**
     * @param UserAccountListRequest $request
     * @return JsonResponse
     */
    public function postUserAccountList(UserAccountListRequest $request): JsonResponse
    {
        return response()->json(Account::where('userId', $request->post('userId'))->get());
    }
}
