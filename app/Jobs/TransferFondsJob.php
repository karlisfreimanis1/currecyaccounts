<?php

namespace App\Jobs;

use App\Models\Transaction;
use DateTime;
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

    //this is just straight up not working as it is written in documentation, maybe missing some php extensions leave it for now
    //https://laravel.com/docs/10.x/queues#max-job-attempts-and-timeout
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 24;
    public $backoff = [60,300, 1800];
    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 24;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = false;

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
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): DateTime
    {
        return now()->addHours(12);
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
                $this->release(now()->addSeconds(60));
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
