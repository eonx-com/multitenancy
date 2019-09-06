<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM\Exceptions;

use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\UniqueValueNotGeneratedException;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\UniqueValueNotGeneratedException
 */
final class UniqueValueNotGeneratedExceptionTest extends AppTestCase
{
    /**
     * Test exception returns the correct codes.
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new UniqueValueNotGeneratedException();

        self::assertSame(1129, $exception->getErrorCode());
        self::assertSame(11, $exception->getErrorSubCode());
    }
}
