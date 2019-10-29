<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Search\Exceptions;

use LoyaltyCorp\Multitenancy\Services\Search\Exceptions\InvalidSearchPathException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Search\Exceptions\InvalidSearchPathException
 */
final class InvalidSearchPathExceptionTest extends TestCase
{
    /**
     * Tests exception codes.
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new InvalidSearchPathException();

        self::assertSame(110, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
    }
}
