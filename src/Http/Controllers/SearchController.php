<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Http\Controllers;

use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Search\Interfaces\ProviderAwareRequestProxyFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SearchController
{
    /**
     * @var \EoneoPay\Externals\HttpClient\Interfaces\ClientInterface
     */
    private $httpClient;

    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Search\Interfaces\ProviderAwareRequestProxyFactoryInterface
     */
    private $requestProxyFactory;

    /**
     * Constructor.
     *
     * phpcs:disable
     *
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ClientInterface $httpClient
     * @param \LoyaltyCorp\Multitenancy\Services\Search\Interfaces\ProviderAwareRequestProxyFactoryInterface $requestProxyFactory
     *
     * phpcs:enable
     */
    public function __construct(
        ClientInterface $httpClient,
        ProviderAwareRequestProxyFactoryInterface $requestProxyFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestProxyFactory = $requestProxyFactory;
    }

    /**
     * Request a search against an elasticsearch index.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @IsGranted({"search:all"})
     */
    public function search(Provider $provider, ServerRequestInterface $request): ResponseInterface
    {
        $searchRequest = $this->requestProxyFactory->createProxyRequest($provider, $request);

        $response = $this->httpClient->sendRequest($searchRequest);

        $response = $response
            // Remove all CORS headers, our application will re-add them
            ->withoutHeader('Access-Control-Allow-Origin')
            ->withoutHeader('Access-Control-Allow-Credentials')
            ->withoutHeader('Access-Control-Allow-Headers')
            ->withoutHeader('Access-Control-Allow-Methods')
            ->withoutHeader('Access-Control-Expose-Headers')
            ->withoutHeader('Access-Control-Max-Age');

        return $response;
    }
}
