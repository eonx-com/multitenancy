<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\TestCases;

use DateInterval;
use DateTime;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use EoneoPay\Externals\Bridge\Laravel\Container;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use EoneoPay\Externals\ORM\EntityManager;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use EoneoPay\Utils\Interfaces\Exceptions\ExceptionInterface;
use EoneoPay\Utils\Interfaces\Exceptions\ValidationExceptionInterface;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Factory;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\TestResult;
use RuntimeException;
use Tests\LoyaltyCorp\Multitenancy\Helpers\ApplicationBootstrapper;
use Tests\LoyaltyCorp\Multitenancy\Helpers\DateIntervalFormatter;
use Throwable;

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
     * SQL queries to create database schema.
     *
     * @var string
     */
    private static $sql;

    /**
     * Entity manager instance.
     *
     * @var \EoneoPay\Externals\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Expected exception class (for exception testing).
     *
     * @var string
     */
    private $exceptionClass;

    /**
     * Expected exception message (for exception testing).
     *
     * @var string
     */
    private $exceptionMessage;

    /**
     * Expected exception message parameters (for exception testing).
     *
     * @var mixed[]
     */
    private $exceptionParameters = [];

    /**
     * Expected exception validation failure keys (for exception testing).
     *
     * @var mixed[]
     */
    private $exceptionValidation = [];

    /**
     * Whether the database has been seeded or not.
     *
     * @var bool
     */
    private $seeded = false;

    /**
     * Validator instance.
     *
     * @var \EoneoPay\Externals\Bridge\Laravel\Validator
     */
    private $validator;

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
        $app = ApplicationBootstrapper::create();

        // Make sure it's an app
        if (($app instanceof Application) === false) {
            new RuntimeException('Unable to create application, can not continue');
        }

        if (self::$metadataCache === null) {
            self::$metadataCache = new ArrayCache();
        }

        $app->make('registry')->getManager()->getMetadataFactory()->setCacheDriver(self::$metadataCache);

        return $app;
    }

    /**
     * Get application container.
     *
     * @return \EoneoPay\Externals\Container\Interfaces\ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return new Container($this->app);
    }

    /**
     * Get doctrine entity manager instance
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Static access to entity manager required to create instance
     */
    protected function getDoctrineEntityManager(): DoctrineEntityManagerInterface
    {
        $paths = [\implode(\DIRECTORY_SEPARATOR, [\realpath(__DIR__), '..', '..', 'src', 'Database', 'Entities'])];
        $setup = new Setup();
        $config = $setup::createAnnotationMetadataConfiguration($paths, true, null, null, false);
        $dbParams = ['driver' => 'pdo_sqlite', 'memory' => true];

        return DoctrineEntityManager::create($dbParams, $config);
    }

    /**
     * Get entity manager
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        if ($this->entityManager !== null) {
            return $this->entityManager;
        }

        // Lazy load database
        $this->createSchema();

        return $this->entityManager;
    }

    /**
     * Get validator instance.
     *
     * @return \EoneoPay\Externals\Validator\Interfaces\ValidatorInterface
     */
    protected function getValidator(): ValidatorInterface
    {
        if ($this->validator !== null) {
            return $this->validator;
        }

        // Get validator factory from app so the extensions are loaded by app service provider
        try {
            $this->validator = new Validator($this->app->make(Factory::class));
        } catch (BindingResolutionException $exception) {
            self::fail(\sprintf('Unable to create validator instance: %s', $exception->getMessage()));
        }

        return $this->validator;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) Complexity required to fully test exceptions
     */
    protected function runTest(): ?TestResult
    {
        // Run parent
        try {
            $testResult = parent::runTest();
        } catch (Throwable $exception) {
            // If no expectations are set, throw original exception
            if ($this->expectsExpectations() === false) {
                throw $exception;
            }

            // Verify exception class matches expectation
            if ($this->exceptionClass !== null) {
                self::assertInstanceOf(
                    $this->exceptionClass,
                    $exception,
                    \sprintf('Failed asserting that exception is instance of %s.', $this->exceptionClass)
                );
            }

            // Verify exception message matches expectation
            if ($this->exceptionMessage !== null) {
                self::assertSame(
                    $this->exceptionMessage,
                    $exception->getMessage(),
                    \sprintf(
                        'Failed asserting that exception message %s is %s.',
                        $exception->getMessage(),
                        $this->exceptionMessage
                    )
                );
            }

            /**
             * @var \EoneoPay\Utils\Interfaces\Exceptions\ExceptionInterface|\Throwable $exception
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            // Test parameters if requested
            if (($exception instanceof ExceptionInterface) === true && \count($this->exceptionParameters) > 0) {
                self::assertSame(
                    $exception->getMessageParameters(),
                    $this->exceptionParameters,
                    'Failed asserting that exception parameters match expectation.'
                );
            }

            /**
             * @var \EoneoPay\Utils\Interfaces\Exceptions\ValidationExceptionInterface|\Throwable $exception
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            // Test validation keys if requested
            if (($exception instanceof ValidationExceptionInterface) === true &&
                \count($this->exceptionValidation) > 0) {
                self::assertSame(
                    \array_keys($exception->getErrors()),
                    $this->exceptionValidation,
                    'Failed asserting that keys from exception validation match expectation.'
                );
            }

            return null;
        }

        // If expectations are set, fail as they should've thrown
        if ($this->expectsExpectations() === true) {
            self::assertThat(
                null,
                new ExceptionConstraint($this->exceptionClass)
            );
        }

        return $testResult;
    }

    /**
     * @param string $class The exception class
     * @param string $message The exception message
     * @param mixed[]|null $parameters Parameters for the exception message
     * @param mixed[]|null $validationKeys Keys returned for a validation failure
     *
     * @return void
     */
    protected function setExpectedException(
        string $class,
        string $message,
        ?array $parameters = null,
        ?array $validationKeys = null
    ): void {
        $this->exceptionClass = $class;
        $this->exceptionMessage = $message;
        $this->exceptionParameters = $parameters ?? [];
        $this->exceptionValidation = $validationKeys ?? [];
    }

    /**
     * Lazy load database schema only when required
     *
     * @return void
     */
    private function createSchema(): void
    {
        // If schema is already created, return
        if ($this->seeded === true) {
            return;
        }

        // Create schema
        try {
            $entityManager = $this->getDoctrineEntityManager();

            // If schema hasn't been defined, define it, this will happen once per run
            if (self::$sql === null) {
                $tool = new SchemaTool($entityManager);
                $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
                self::$sql = \implode(';', $tool->getCreateSchemaSql($metadata));
            }

            $entityManager->getConnection()->exec(self::$sql);

            $this->entityManager = new EntityManager($entityManager);
        } catch (Exception $exception) {
            self::fail(\sprintf('Exception thrown when creating database schema: %s', $exception->getMessage()));
        }

        $this->seeded = true;
    }

    /**
     * Determine if we are checking for exceptions.
     *
     * @return bool
     */
    private function expectsExpectations(): bool
    {
        return $this->exceptionClass !== null ||
            $this->exceptionMessage !== null ||
            \count($this->exceptionParameters) > 0;
    }
}
