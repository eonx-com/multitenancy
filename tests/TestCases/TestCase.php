<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\TestCases;

use DateInterval;
use DateTime;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Tests\LoyaltyCorp\Multitenancy\Helpers\ApplicationInstantiator;
use Tests\LoyaltyCorp\Multitenancy\Helpers\DateIntervalFormatter;

/**
 * @noinspection EfferentObjectCouplingInspection
 *
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Centralised logic for all tests
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) Complexity required for testing
 * @SuppressWarnings(PHPMD.NumberOfChildren) All tests extend this class
 * @SuppressWarnings(PHPMD.TooManyFields) Required for base test functionality
 */
class TestCase extends BaseTestCase
{
    /**
     * A doctrine cache for metadata storage across test runs.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    private static $metadataCache;

    /**
     * Uses assertSame on $expected and $actual arrays after converting DateTime and DateInterval
     * objects to strings.
     *
     * @param mixed[] $expected
     * @param mixed[] $actual
     * @param string|null $message
     *
     * @return void
     */
    public static function assertArraySameWithDates(array $expected, array $actual, ?string $message = null): void
    {
        $intervalFormat = new DateIntervalFormatter();
        $format = static function (&$value) use ($intervalFormat): void {
            if (($value instanceof DateTime) === true) {
                /**
                 * @var \DateTime $value
                 *
                 * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises ===
                 */
                $value = $value->format(DateTime::RFC3339);
            }

            if (($value instanceof DateInterval) === true) {
                /**
                 * @var \DateInterval $value
                 *
                 * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises ===
                 */
                $value = $intervalFormat->format($value);
            }
        };

        \array_walk_recursive($expected, $format);
        \array_walk_recursive($actual, $format);

        self::assertSame($expected, $actual, $message ?? '');
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        return ApplicationInstantiator::create();
    }
}
