<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Exceptions;

use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException
 */
final class InvalidEntityOwnershipExceptionTest extends AppTestCase
{
    /**
     * Test exception returns the correct codes.
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new InvalidEntityOwnershipException();

        self::assertSame(1129, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
    }
}
