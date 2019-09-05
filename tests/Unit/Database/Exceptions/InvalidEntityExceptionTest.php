<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Exceptions;

use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityException
 */
final class InvalidEntityExceptionTest extends AppTestCase
{
    /**
     * Test exception returns the correct codes
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new InvalidEntityException();

        self::assertSame(1129, $exception->getErrorCode());
        self::assertSame(3, $exception->getErrorSubCode());
    }
}
