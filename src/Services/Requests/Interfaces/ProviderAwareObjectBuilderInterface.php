<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Requests\Interfaces;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface;

/**
 * This interface is a provider aware duplicate of the ObjectBuilderInterface
 * that is exposed by the requesthandlers package.
 *
 * Implementations of this service should ensure that the
 * RequestBodyContextConfigurator::MULTITENANCY_PROVIDER key is set on the context
 * array with the value of the $provider.
 */
interface ProviderAwareObjectBuilderInterface
{
    /**
     * Builds a valid Request Object given the supplied json and optional additional
     * context.
     *
     * @param string $objectClass
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param string $json
     * @param mixed[]|null $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     */
    public function build(
        string $objectClass,
        Provider $provider,
        string $json,
        ?array $context = null
    ): RequestObjectInterface;

    /**
     * Used to build a request object with an array of context. Is used if there
     * is no point to providing JSON and properties are directly provided instead.
     *
     * @param string $objectClass
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param mixed[] $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     */
    public function buildWithContext(string $objectClass, Provider $provider, array $context): RequestObjectInterface;
}
