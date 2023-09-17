<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTransactionsRequest;
use App\Http\Requests\TransferFondsRequest;
use App\Jobs\TransferFondsJob;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * @param AccountTransactionsRequest $request
     * @return JsonResponse
     */
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

    /**
     * @param TransferFondsRequest $request
     * @return JsonResponse
     */
    public function postTransferFonds(TransferFondsRequest $request): JsonResponse
    {
        //this goes out of controller soon todo
        $currentBalance = Account::where('id', $request->post()['accountIdFrom'])->pluck('currentBalance')->first();
        $currentReservedMoney = Transaction::where('accountIdFrom', $request->post()['accountIdFrom'])->where(
            'status',
            0
        )->sum('valueFrom'); //todo try attempts
        $availableCredits = $currentBalance - $currentReservedMoney;

        if ($availableCredits < $request->post()['value']) {
            return response()->json([
                'errors' => [
                    'Insufficient account balance! Available:' . $availableCredits . ' required:' . $request->post(
                    )['value']
                ]
            ]);
        }
        //smells like duplicate risk //todo do it better if time allows
        $transaction = Transaction::Create([
            'accountIdFrom' => $request->post()['accountIdFrom'],
            'accountIdTo' => $request->post()['accountIdTo'],
            'valueFrom' => $request->post()['value'],
        ]);
        TransferFondsJob::dispatch($transaction);
        return response()->json($transaction);
    }
}
