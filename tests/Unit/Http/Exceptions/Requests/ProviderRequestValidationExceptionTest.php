<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Exceptions\Requests;

use LoyaltyCorp\Multitenancy\Http\Exceptions\Requests\ProviderRequestValidationException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Http\Exceptions\Requests\ProviderRequestValidationException
 */
final class ProviderRequestValidationExceptionTest extends AppTestCase
{
    /**
     * Tests that the exception codes match the expected.
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new ProviderRequestValidationException();

        self::assertSame(6100, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
    }
}
