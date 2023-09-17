<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    private const URI = 'api/account-list';

    /** @test */
    public function emptyList()
    {
        $user = User::factory()->create();
        $response = $this->postJson($this::URI, ['userId' => $user->id]);

        $response->assertStatus(200)
            ->assertExactJson([]);
        $user->delete();
    }

    /** @test */
    public function jsonStructure()
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create();
        $account = Account::factory()->create(
            [
                'userId' => $user->id,
                'currencyId' => $currency->id,
                'currentBalance' => 100
            ]
        );
        $response = $this->postJson($this::URI, ['userId' => $user->id]);

        $response->assertStatus(200)
            ->assertExactJson([
                [
                    'currencyId' => $currency->id,
                    'currentBalance' => '100.000000',
                    'id' => $account->id,
                    'userId' => $user->id,
                ]
            ]);
        $user->delete();
        $currency->delete();
        $account->delete();
    }

    /** @test */
    public function noneIntegerInput(): void
    {
        $response = $this->postJson($this::URI, ['userId' => 'zzz']);

        $response->assertStatus(200)
            ->assertExactJson([
                'errors' => [
                    'userId' => [
                        'The user id field must be an integer.',
                        'User not found id:zzz'
                    ]
                ]
            ]);
    }

    /** @test */
    public function userDoNotExists(): void
    {
        $response = $this->postJson($this::URI, ['userId' => 0]);

        $response->assertStatus(200)
            ->assertExactJson([
                'errors' => [
                    'userId' => [
                        'User not found id:0'
                    ]
                ]
            ]);
    }
}
