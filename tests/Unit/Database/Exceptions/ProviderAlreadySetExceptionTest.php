<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Exceptions;

use LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException
 */
final class ProviderAlreadySetExceptionTest extends AppTestCase
{
    /**
     * Test exception returns the correct codes.
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new ProviderAlreadySetException();

        self::assertSame(1129, $exception->getErrorCode());
        self::assertSame(2, $exception->getErrorSubCode());
    }
}
