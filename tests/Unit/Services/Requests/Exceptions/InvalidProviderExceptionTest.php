<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Requests\Exceptions;

use LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException
 */
final class InvalidProviderExceptionTest extends AppTestCase
{
    /**
     * Tests exception codes.
     *
     * @return void
     */
    public function testMethods(): void
    {
        $exception = new InvalidProviderException();

        self::assertSame(101, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
    }
}
