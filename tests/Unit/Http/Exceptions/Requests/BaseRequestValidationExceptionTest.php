<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Exceptions\Requests;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Exceptions\Requests\BaseRequestValidationExceptionStub;
use Tests\LoyaltyCorp\Multitenancy\TestCase;

/**
 * @covers \LoyaltyCorp\Mulitenancy\Http\Exceptions\Requests\BaseRequestValidationException
 */
class BaseRequestValidationExceptionTest extends TestCase
{
    /**
     * Tests the base exception method.
     *
     * @return void
     */
    public function testBaseException(): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'Message',
            'Message',
            [],
            'root',
            'path',
            'invalid'
        ));

        $expected = [
            'path' => [
                'Message'
            ]
        ];

        $exception = new BaseRequestValidationExceptionStub($violations);

        self::assertSame('exceptions.http.request_body.validation_failures', $exception->getMessage());

        self::assertSame($expected, $exception->getErrors());
    }
}
