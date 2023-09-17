<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTransactionsRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function postAccountTransactions(AccountTransactionsRequest $request): JsonResponse
    {
        $limit = empty($request->post('limit')) ? 100 : $request->post('limit');
        $offset = empty($request->post('offset')) ? 0 : $request->post('offset');

        return response()->json(
            Transaction::where('accountIdFrom', $request->post('accountId'))
                ->orWhere('accountIdTo', $request->post('accountId'))
                ->offset($offset)
                ->limit($limit)
                ->orderBy('timeCreated', 'DESC')
                ->get()
        );
    }
}
