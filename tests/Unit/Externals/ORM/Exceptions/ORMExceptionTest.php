<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM\Exceptions;

use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException
 */
final class ORMExceptionTest extends AppTestCase
{
    /**
     * Test exception returns the correct codes
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new ORMException();

        self::assertSame(9029, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
        self::assertSame('A database error occurred.', $exception->getErrorMessage());
    }
}
