<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Requests;

use FOS\RestBundle\Context\Context;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\RequestHandlers\Request\Interfaces\ContextConfiguratorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This service exists to set the provider from the request attributes into the
 * serialiser context when the RequestBodyParamConverter runs.
 *
 * It allows for any serialiser extensions to have access to the provider that
 * came in from the request.
 */
final class RequestBodyContextConfigurator implements ContextConfiguratorInterface
{
    /**
     * The context key that will be used to store the provider inside the serialiser
     * context.
     *
     * @const string
     */
    public const MULTITENANCY_PROVIDER = 'multitenancy_provider';

    /**
     * {@inheritdoc}
     */
    public function configure(Context $context, Request $request): void
    {
        $provider = $request->attributes->get('provider');

        // If we dont have a provider in the provider key, we wont add anything.
        if ($provider instanceof Provider === false) {
            return;
        }

        $context->setAttribute(self::MULTITENANCY_PROVIDER, $provider);
    }
}
