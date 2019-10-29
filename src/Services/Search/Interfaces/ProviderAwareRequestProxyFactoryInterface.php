<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Search\Interfaces;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use Psr\Http\Message\RequestInterface;

interface ProviderAwareRequestProxyFactoryInterface
{
    /**
     * Takes a RequestInterface made to the application and rewrites headers
     * and hostnames to send the request to the configured Elasticsearch system.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function createProxyRequest(Provider $provider, RequestInterface $request): RequestInterface;
}
