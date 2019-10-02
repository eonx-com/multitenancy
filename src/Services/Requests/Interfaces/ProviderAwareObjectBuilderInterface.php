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
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param string $objectClass
     * @param string $json
     * @param mixed[]|null $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     */
    public function build(
        Provider $provider,
        string $objectClass,
        string $json,
        ?array $context = null
    ): RequestObjectInterface;

    /**
     * Used to build a request object with an array of context. Is used if there
     * is no point to providing JSON and properties are directly provided instead.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param string $objectClass
     * @param mixed[] $context
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     */
    public function buildWithContext(Provider $provider, string $objectClass, array $context): RequestObjectInterface;
}
