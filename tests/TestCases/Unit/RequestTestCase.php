<?php /** @noinspection PhpUndefinedClassInspection date formatter and locale are declared multiple times */
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\TestCases\Unit;

use EoneoPay\Utils\AnnotationReader;
use LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException;
use LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface;
use LoyaltyCorp\RequestHandlers\TestHelper\Exceptions\ValidationFailedException;
use LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolationList;
use Tests\LoyaltyCorp\Multitenancy\TestCases\TestCase;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Centralised logic for all tests
 * @SuppressWarnings(PHPMD.NumberOfChildren) All request object test cases extend this.
 */
abstract class RequestTestCase extends TestCase
{
    /**
     * Request object test helper - this is marked as @internal to ensure it's used for test purposes only
     *
     * @var \LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper
     */
    private $requestTestHelper;

    /**
     * Returns the class to be tested.
     *
     * @return string
     */
    abstract public function getRequestClass(): string;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->requestTestHelper = new RequestObjectTestHelper($this->app);
    }

    /**
     * Tests that the Request has a valid Exception class
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testPropertiesHaveTypeAssertion(): void
    {
        $class = new ReflectionClass(static::getRequestClass());
        $classProperties = $class->getProperties();

        $reader = $this->app->make(AnnotationReader::class);

        $untypedProperties = \array_filter(
            $classProperties,
            static function (ReflectionProperty $property) use ($reader): bool {
                $type = $reader->getPropertyAnnotation($property, Type::class);

                if ($type instanceof Type === false) {
                    return false;
                }

                /**
                 * @var \Symfony\Component\Validator\Constraints\Type $type
                 *
                 * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises ===
                 */

                return \in_array('PreValidate', $type->groups, true) === false;
            }
        );

        $propertyNames = \array_map(static function (ReflectionProperty $property): string {
            return $property->getName();
        }, $untypedProperties);

        static::assertSame([], $propertyNames, 'Properties without types');
    }

    /**
     * Tests that the Request has a valid Exception class.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testRequestExceptionClass(): void
    {
        $class = static::getRequestClass();
        $reflection = new \ReflectionClass($class);

        $instance = $reflection->newInstanceWithoutConstructor();

        self::assertInstanceOf(RequestObjectInterface::class, $instance);

        /** @var \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface $instance */
        $exceptionClass = $instance::getExceptionClass();

        $exception = new $exceptionClass(new ConstraintViolationList());

        self::assertInstanceOf(RequestValidationException::class, $exception);
    }

    /**
     * Asserts that the request object has the expected properties.
     *
     * @param \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface $instance
     * @param mixed[] $expected
     *
     * @return void
     */
    protected function assertRequestProperties(RequestObjectInterface $instance, array $expected): void
    {
        $actual = $this->requestTestHelper->getRequestProperties($instance);

        self::assertArraySameWithDates($expected, $actual);
    }

    /**
     * Builds a failing request, and returns a formatted validation failures array.
     *
     * @param string $jsonIn
     * @param mixed[]|null $context
     *
     * @return mixed[]
     */
    protected function buildFailingRequest(string $jsonIn, ?array $context = null): array
    {
        return $this->requestTestHelper->buildFailingRequest(
            static::getRequestClass(),
            $jsonIn,
            $context
        );
    }

    /**
     * Deserializes json into an entity.
     *
     * @param string $jsonIn
     * @param mixed[]|null $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     */
    protected function buildUnvalidatedRequest(
        string $jsonIn,
        ?array $context = null
    ): RequestObjectInterface {
        return $this->requestTestHelper->buildUnvalidatedRequest(
            static::getRequestClass(),
            $jsonIn,
            $context
        );
    }

    /**
     * Tests object creation is successful.
     *
     * @param string $jsonIn
     * @param mixed[]|null $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     */
    protected function buildValidatedRequest(
        string $jsonIn,
        ?array $context = null
    ): RequestObjectInterface {
        try {
            return $this->requestTestHelper->buildValidatedRequest(
                static::getRequestClass(),
                $jsonIn,
                $context
            );
        } catch (ValidationFailedException $exception) {
            if (\method_exists($exception->getViolations(), '__toString') === false) {
                self::fail('Validation exception occurred while building a validated request');
            }

            self::fail((string)$exception->getViolations());
        }

        // This exception is only here because some lints don't see self::fail as a return point
        throw new RuntimeException('An error occurred.');
    }
}
