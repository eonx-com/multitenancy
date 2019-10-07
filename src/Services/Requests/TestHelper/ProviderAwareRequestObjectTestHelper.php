<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Requests\TestHelper;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Requests\Interfaces\ProviderAwareObjectBuilderInterface;
use LoyaltyCorp\Multitenancy\Services\Requests\RequestBodyContextConfigurator;
use LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException;
use LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface;
use LoyaltyCorp\RequestHandlers\Serializer\PropertyNormalizer;
use LoyaltyCorp\RequestHandlers\TestHelper\Exceptions\ValidationFailedException;
use LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * This test helper is a reimplementation of the TestHelper provided by RequestHandlers
 * that adds Provider functionality that may be required in a multi tenancy application.
 *
 * This class should not be used by any normal services.
 *
 * @internal
 *
 * @see \LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper
 */
final class ProviderAwareRequestObjectTestHelper
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Requests\Interfaces\ProviderAwareObjectBuilderInterface
     */
    private $objectBuilder;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var \LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper
     */
    private $testHelper;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\Multitenancy\Services\Requests\Interfaces\ProviderAwareObjectBuilderInterface $objectBuilder
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param \LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper $testHelper
     */
    public function __construct(
        ProviderAwareObjectBuilderInterface $objectBuilder,
        SerializerInterface $serializer,
        /** @noinspection PhpInternalEntityUsedInspection */
        RequestObjectTestHelper $testHelper
    ) {
        $this->objectBuilder = $objectBuilder;
        $this->serializer = $serializer;
        $this->testHelper = $testHelper;
    }

    /**
     * Builds a failing request and returns the validation errors raised by the failure.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param string $class
     * @param string $json
     * @param mixed[]|null $context
     *
     * @return mixed[]
     */
    public function buildFailingRequest(
        Provider $provider,
        string $class,
        string $json,
        ?array $context = null
    ): array {
        try {
            $this->buildValidatedRequest($provider, $class, $json, $context);
        } catch (ValidationFailedException $exception) {
            return $exception->getErrors();
        }

        throw new RuntimeException('There were no validation errors.');
    }

    /**
     * Builds an unvalidated request object. The context property will set and overwrite
     * any properties on the request object with the supplied values.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param string $class
     * @param string $json
     * @param mixed[]|null $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     */
    public function buildUnvalidatedRequest(
        Provider $provider,
        string $class,
        string $json,
        ?array $context = null
    ): RequestObjectInterface {
        /** @var \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface $instance */
        $instance = $this->serializer->deserialize(
            $json,
            $class,
            'json',
            [
                PropertyNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                PropertyNormalizer::EXTRA_PARAMETERS => $context ?? [],
                RequestBodyContextConfigurator::MULTITENANCY_PROVIDER => $provider,
            ]
        );

        return $instance;
    }

    /**
     * Builds a validated request object. The context property will set and overwrite
     * any properties on the request object with the supplied values.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param string $class
     * @param string $json
     * @param mixed[]|null $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     *
     * @throws \LoyaltyCorp\RequestHandlers\TestHelper\Exceptions\ValidationFailedException
     */
    public function buildValidatedRequest(
        Provider $provider,
        string $class,
        string $json,
        ?array $context = null
    ): RequestObjectInterface {
        try {
            return $this->objectBuilder->build(
                $provider,
                $class,
                $json,
                $context
            );
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (RequestValidationException $exception) {
            throw new ValidationFailedException(
                $exception->getViolations(),
                'Got validation failures when trying to build a validated request.',
                null,
                null,
                $exception
            );
        }
    }

    /**
     * Returns an array of properties and their values when those properties have getters.
     *
     * @param \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface $object
     *
     * @return mixed[]
     */
    public function getRequestProperties(RequestObjectInterface $object): array
    {
        return $this->testHelper->getRequestProperties($object);
    }
}
