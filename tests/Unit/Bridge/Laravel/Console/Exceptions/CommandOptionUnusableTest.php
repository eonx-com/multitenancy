<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Bridge\Laravel\Console\Exceptions;

use LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Exceptions\CommandOptionUnusable;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Exceptions\CommandOptionUnusable
 */
final class CommandOptionUnusableTest extends AppTestCase
{
    /**
     * Test exception returns the correct codes.
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new CommandOptionUnusable();

        self::assertSame(1130, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
    }
}
