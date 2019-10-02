<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Requests;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareObjectBuilder;
use LoyaltyCorp\RequestHandlers\Builder\Interfaces\ObjectValidatorInterface;
use LoyaltyCorp\RequestHandlers\Exceptions\MisconfiguredSerializerException;
use LoyaltyCorp\RequestHandlers\Exceptions\UnsupportedClassException;
use stdClass;
use Symfony\Component\Serializer\SerializerInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Requests\RequestStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\RequestHandlers\ObjectValidatorStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Symfony\SerializerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareObjectBuilder
 */
final class ProviderAwareObjectBuilderTest extends AppTestCase
{
    /**
     * Tests that build returns the object the serializer returned.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\UnsupportedClassException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\MisconfiguredSerializerException
     */
    public function testBuild(): void
    {
        $expected = new RequestStub();
        $provider = new Provider('id', 'name');

        $serializer = new SerializerStub($expected);
        $validator = new ObjectValidatorStub();
        $builder = $this->getBuilder($serializer, $validator);

        $result = $builder->build($provider, RequestStub::class, '');

        self::assertSame($expected, $result);
    }

    /**
     * Tests the build happy path.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\UnsupportedClassException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\MisconfiguredSerializerException
     */
    public function testBuildNotRequestObject(): void
    {
        $provider = new Provider('id', 'name');

        $serializer = new SerializerStub(new stdClass());
        $validator = new ObjectValidatorStub();
        $builder = $this->getBuilder($serializer, $validator);

        $this->expectException(UnsupportedClassException::class);
        $this->expectExceptionMessage(
            'The supplied class "stdClass" is not supported. It must be an instance of "LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface"' // phpcs:ignore
        );

        $builder->build($provider, stdClass::class, '');
    }

    /**
     * Tests the buildWithContext returns the expected.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\UnsupportedClassException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\MisconfiguredSerializerException
     */
    public function testBuildWithContext(): void
    {
        $provider = new Provider('id', 'name');

        $expected = new RequestStub();

        $serializer = new SerializerStub($expected);
        $validator = new ObjectValidatorStub();
        $builder = $this->getBuilder($serializer, $validator);

        $result = $builder->buildWithContext($provider, RequestStub::class, []);

        self::assertSame($expected, $result);
    }

    /**
     * Tests the build failure when wrong object is returned.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\UnsupportedClassException
     * @throws \LoyaltyCorp\RequestHandlers\Exceptions\MisconfiguredSerializerException
     */
    public function testBuildWrongObject(): void
    {
        $provider = new Provider('id', 'name');

        $serializer = new SerializerStub(new stdClass());
        $validator = new ObjectValidatorStub();
        $builder = $this->getBuilder($serializer, $validator);

        $this->expectException(MisconfiguredSerializerException::class);
        $this->expectExceptionMessage(
            'The serializer returned an object of type "stdClass" but it is not an instance of "Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Requests\RequestStub"' // phpcs:ignore
        );

        $builder->build($provider, RequestStub::class, '');
    }

    /**
     * Gets the builder under test.
     *
     * @param \Symfony\Component\Serializer\SerializerInterface|null $serializer
     * @param \LoyaltyCorp\RequestHandlers\Builder\Interfaces\ObjectValidatorInterface|null $validator
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareObjectBuilder
     */
    private function getBuilder(
        ?SerializerInterface $serializer = null,
        ?ObjectValidatorInterface $validator = null
    ): ProviderAwareObjectBuilder {
        return new ProviderAwareObjectBuilder(
            $serializer ?? new SerializerStub(),
            $validator ?? new ObjectValidatorStub()
        );
    }
}
