<?php

namespace Tests\Feature;

use App\Http\Requests\ApiJsonRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\TestCase;

class ApiJsonListRequestTest extends TestCase
{
    /** @test */
    public function it_throws_http_response_exception_on_failed_validation()
    {
        $request = new ApiJsonRequest();

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validator->expects($this->once())
            ->method('errors')
            ->willReturn(['field_name' => ['This field is required.']]);

        try {
            $request->failedValidation($validator);
        } catch (HttpResponseException $exception) {
            $this->assertJson($exception->getResponse()->getContent());
            $data = json_decode($exception->getResponse()->getContent(), true);
            $this->assertArrayHasKey('errors', $data);
            $this->assertEquals(['field_name' => ['This field is required.']], $data['errors']);
            return;
        }

        $this->fail('HttpResponseException was not thrown');
    }
}
