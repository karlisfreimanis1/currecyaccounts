<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $currencies = Currency::all();
        if ($currencies->isEmpty()) {
            $response = Http::get(env('API_EXCHANGE_URL') . '/letest');
            if ($response->ok() && !empty($response['rates'])) {
                foreach ($response['rates'] as $code => $rates) {
                    $currencies[] = Currency::firstOrCreate([
                        'code' => $code
                    ]);
                }
            }
        }
        $users = User::all();
        if ($users->isEmpty()) {
            foreach (range(0, 20) as $row) {
                $users[] = User::factory()->create();
            }
        }

        $accounts = Account::all();
        if ($accounts->isEmpty()) {
            $newAccounts = [];
            foreach ($users as $user) {
                foreach ($currencies->random(3) as $currency) {
                    $newAccounts[] = [
                        'userId' => $user->id,
                        'currencyId' => $currency->id,
                        'currentBalance' => rand(0, 10 ^ 4)
                    ];
                }
            }
            Account::insert($newAccounts);
        }

        $transactions = Transaction::all();
        if ($transactions->isEmpty()) {
            foreach ($accounts as $account) {
                $newTransactions = [];
                foreach (range(0, rand(1, 150)) as $row) {
                    $randAcc = $accounts->random(1)->first();
                    if ($randAcc->id !== $account->id) {
                        $newTransactions[] = [
                            'accountIdFrom' => $account->id,
                            'accountIdTo' => $randAcc->id,
                            'valueFrom' => rand(0, 100),
                            'valueTo' => rand(0, 100),
                            //this will make inaccurate transactions but this is just for volume in transaction list, pushing api to limit just for volume not smart
                            'status' => 1,
                            //processed
                            'timeCreated' => date('Y-m-d H:i:s'),
                            'timeProcessed' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
                Transaction::insert($newTransactions);
            }
        }
    }
}
