<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\UserExists;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserExistsRuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function validateUserExists(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a Validator instance with the UserExists rule
        $validator = Validator::make(['user_id' => $user->id], [
            'user_id' => ['required', new UserExists],
        ]);

        // Assert that validation passes
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validateUserDoesNotExist(): void
    {
        // Create a Validator instance with the UserExists rule
        $validator = Validator::make(['user_id' => 123], [
            'user_id' => ['required', new UserExists],
        ]);

        // Assert that validation fails
        $this->assertTrue($validator->fails());

        // Check if the error message is correct
        $errors = $validator->errors();
        $this->assertEquals(['user_id' => ['User not found id:123']], $errors->toArray());
    }
}
