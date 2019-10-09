<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Requests\TestHelper;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareObjectBuilder;
use LoyaltyCorp\Multitenancy\Services\Requests\TestHelper\ProviderAwareRequestObjectTestHelper;
use LoyaltyCorp\RequestHandlers\Builder\ObjectBuilder;
use LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException;
use LoyaltyCorp\RequestHandlers\TestHelper\Exceptions\ValidationFailedException;
use /** @noinspection PhpInternalEntityUsedInspection */ LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper;
use RuntimeException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Exceptions\Requests\BaseRequestValidationExceptionStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Requests\RequestStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\RequestHandlers\ObjectValidatorStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Symfony\SerializerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Required to test
 *
 * @covers \LoyaltyCorp\Multitenancy\Services\Requests\TestHelper\ProviderAwareRequestObjectTestHelper
 */
final class ProviderAwareRequestObjectTestHelperTest extends AppTestCase
{
    /**
     * Tests buildFailedRequest.
     *
     * @return void
     */
    public function testBuildFailedRequest(): void
    {
        $provider = new Provider('id', 'name');
        $object = new RequestStub();
        $expected = [
            'property' => ['Message'],
        ];

        $helper = $this->getHelper($object, new BaseRequestValidationExceptionStub(
            new ConstraintViolationList([
                new ConstraintViolation(
                    'Message',
                    'message',
                    [],
                    '',
                    'property',
                    ''
                ),
            ])
        ));

        $result = $helper->buildFailingRequest(RequestStub::class, $provider, '');

        self::assertSame($expected, $result);
    }

    /**
     * Tests buildFailedRequest.
     *
     * @return void
     */
    public function testBuildFailedRequestNotFailing(): void
    {
        $provider = new Provider('id', 'name');
        $object = new RequestStub();

        $helper = $this->getHelper($object);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There were no validation errors.');

        $helper->buildFailingRequest(RequestStub::class, $provider, '');
    }

    /**
     * Tests unvalidated request creation.
     *
     * @return void
     */
    public function testGetRequestProperties(): void
    {
        $object = new RequestStub();
        $expected = [
            'property' => 'test',
        ];

        $helper = $this->getHelper($object);

        $properties = $helper->getRequestProperties($object);

        self::assertSame($expected, $properties);
    }

    /**
     * Tests unvalidated request creation.
     *
     * @return void
     */
    public function testUnvalidatedRequest(): void
    {
        $provider = new Provider('id', 'name');

        $object = new RequestStub();

        $helper = $this->getHelper($object);

        $thing = $helper->buildUnvalidatedRequest(RequestStub::class, $provider, '');

        self::assertSame($object, $thing);
    }

    /**
     * Tests validated request creation.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\RequestHandlers\TestHelper\Exceptions\ValidationFailedException
     */
    public function testValidatedRequest(): void
    {
        $provider = new Provider('id', 'name');

        $object = new RequestStub();

        $helper = $this->getHelper($object);

        $thing = $helper->buildValidatedRequest(RequestStub::class, $provider, '');

        self::assertSame($object, $thing);
    }

    /**
     * Tests validated request creation when theres a validation failure.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\RequestHandlers\TestHelper\Exceptions\ValidationFailedException
     */
    public function testValidatedRequestWhenUnvalidated(): void
    {
        $provider = new Provider('id', 'name');

        $object = new RequestStub();

        $helper = $this->getHelper($object, new BaseRequestValidationExceptionStub(
            new ConstraintViolationList()
        ));

        $this->expectException(ValidationFailedException::class);

        $helper->buildValidatedRequest(RequestStub::class, $provider, '');
    }

    /**
     * Gets helper under test.
     *
     * @param object|\Throwable $object
     * @param \LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException|null $exception
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Requests\TestHelper\ProviderAwareRequestObjectTestHelper
     */
    private function getHelper(
        $object = null,
        ?RequestValidationException $exception = null
    ): ProviderAwareRequestObjectTestHelper {
        $serializer = new SerializerStub($object);
        $validator = new ObjectValidatorStub($exception);

        $objectBuilder = new ObjectBuilder($serializer, $validator);

        return new ProviderAwareRequestObjectTestHelper(
            new ProviderAwareObjectBuilder($serializer, $validator),
            $serializer,
            new RequestObjectTestHelper($objectBuilder, $serializer)
        );
    }
}
