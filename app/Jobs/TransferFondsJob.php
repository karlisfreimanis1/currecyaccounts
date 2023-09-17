<?php

namespace App\Jobs;

use App\Models\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransferFondsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function maxTries()
    {
        return 5;
    }

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 12;

    /**
     * @var Transaction
     */
    public $transaction;


    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction->withoutRelations();
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 300, 1800, 3600];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $exchangeRate = 1;
            $currencyFromCode = $this->transaction->accountFrom()->get()->first()->accountCurrency()->get()->first()->code;
            $currencyToCode = $this->transaction->accountTo()->get()->first()->accountCurrency()->get()->first()->code;
            if($currencyFromCode === $currencyToCode) {
                $this->processTransaction(
                    $this->prepareTransaction($this->transaction, $exchangeRate),
                );
                return;
                //skip api bottleneck when $exchangeRate === 1
            }

            $response = Http::get(env('API_EXCHANGE_URL') . '/letest?base=' . $currencyFromCode);
            if ($response->ok() && !empty($response['rates'])) {
                $exchangeRate = $response['rates'][$currencyToCode];
            } else {
                $this->fail();
            }

            $this->processTransaction(
                $this->prepareTransaction($this->transaction, $exchangeRate),
            );
        } catch (Exception $e) {
            //todo better handle exception types
            $this->fail();
        }
    }

    /**
     * @param Transaction $transaction
     * @param float $exchangeRate
     * @return Transaction
     */
    private function prepareTransaction(Transaction $transaction, float $exchangeRate = 1) : Transaction
    {
        $processedTransaction = $transaction;
        $transaction->setAttribute('status', 1);
        $transaction->setAttribute('valueTo', $transaction->valueFrom*$exchangeRate);
        $transaction->setAttribute('timeProcessed', Carbon::now()->format('Y-m-d H:i:s'));
        return $processedTransaction;
    }

    private function processTransaction(Transaction $transaction) {
        //looks extremely slow for scale at least no need to worry about lost connection between smaller query
        $query = 'UPDATE transactions'
            . ' INNER JOIN accounts as accFrom ON accFrom.id=transactions.accountIdFrom'
            . ' INNER JOIN accounts as accTo ON accTo.id=transactions.accountIdTo'
            . ' SET'
            . ' transactions.status=' . $transaction->status
            . ', transactions.valueTo=' . $transaction->valueTo
            . ', transactions.timeProcessed=' . "'" . $transaction->timeProcessed . "'"
            . ', accFrom.currentBalance=accFrom.currentBalance-' . $transaction->valueFrom
            . ', accTo.currentBalance=accTo.currentBalance+' . $transaction->valueTo
            . ' WHERE GREATEST(0, accFrom.currentBalance - ' . $transaction->valueFrom . ')'
            . ' AND transactions.id=' . $transaction->id
            . ' AND transactions.status=0';
        DB::statement($query);
    }
}
