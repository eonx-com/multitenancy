<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Exceptions;

use LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException
 */
final class ProviderNotSetExceptionTest extends AppTestCase
{
    /**
     * Test exception returns the correct codes
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new ProviderNotSetException();

        self::assertSame(5000, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
    }
}
