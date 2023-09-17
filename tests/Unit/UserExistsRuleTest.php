<?php

namespace Tests\Unit;

use App\Rules\UserExists;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserExistsRuleTest extends TestCase
{
    /** @test */
    public function validateUserDoesNotExist(): void
    {
        // Create a Validator instance with the UserExists rule
        $validator = Validator::make(['user_id' => 0], [
            'user_id' => ['required', new UserExists],
        ]);

        // Assert that validation fails
        $this->assertTrue($validator->fails());

        // Check if the error message is correct
        $errors = $validator->errors();
        $this->assertEquals(['user_id' => ['User not found id:0']], $errors->toArray());
    }
}
