<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Search;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Search\Interfaces\ProviderAwareRequestProxyFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Uri;

class ProviderAwareRequestProxyFactory implements ProviderAwareRequestProxyFactoryInterface
{
    /**
     * The full URI to hit the Elasticsearch system. ie: https://admin:admin@elasticsearch:9200.
     *
     * @var string
     */
    private $elasticHost;

    /**
     * Constructor.
     *
     * @param string $elasticHost
     */
    public function __construct(string $elasticHost)
    {
        $this->elasticHost = $elasticHost;
    }

    /**
     * {@inheritdoc}
     */
    public function createProxyRequest(Provider $provider, RequestInterface $request): RequestInterface
    {
        // Remove prefixed /search from our routes
        $originalUri = $request->getUri();
        $searchPath = \mb_substr($originalUri->getPath(), 7);

        // Build provider specific search path.
        $searchPath = $this->buildProviderSpecificPath($provider, $searchPath);

        // Rewrite the Search Uri with our Elasticsearch host and new search path
        // Note: the $searchUri is also how we define authorisation to be used for this proxying.
        $searchUri = (new Uri($this->elasticHost))
            ->withPath($searchPath)
            ->withQuery($originalUri->getQuery())
            ->withFragment($originalUri->getFragment());

        // Builds a new request target
        $requestTarget = $this->buildRequestTarget($request, $searchPath);

        if (($request instanceof ServerRequestInterface) === true) {
            // If we get a ServerRequest, remove _encoder added by our framework

            /**
             * @var \GuzzleHttp\Psr7\ServerRequest $request
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            $request = $request->withoutAttribute('_encoder');
        }

        // Strip any authentication headers from the request.
        $request = $request->withoutHeader('Authorization');

        $userInfo = $searchUri->getUserInfo();
        if ($userInfo !== '') {
            // If the URI had a userinfo component, add an Authorization header.

            $request = $request->withHeader(
                'Authorization',
                \sprintf('Basic %s', \base64_encode($userInfo))
            );
        }

        return $request
            // Remove the original Content-Length - the underlying Http Client will repopulate
            ->withoutHeader('Content-Length')
            // Force a json content type
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            // Swap out the original request target with our new one
            ->withRequestTarget($requestTarget ?: '/')
            // Replace the URL
            ->withUri($searchUri);
    }

    /**
     * Build provider specific search path.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider entity
     * @param string $searchPath Original search path
     *
     * @return string Provider specific search path
     */
    private function buildProviderSpecificPath(Provider $provider, string $searchPath): string
    {
        $pathtokens = \explode('/', $searchPath);

        // Here we are assuming that the first part of the path will be index name.
        if (\count($pathtokens) >= 2) {
            $pathtokens[1] = \sprintf('%s_%s', $pathtokens[1], \mb_strtolower($provider->getExternalId()));

            return \implode('/', $pathtokens);
        }

        return $searchPath;
    }

    /**
     * Appends any URI querystring to the searchPath for use as the RequestTarget.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string $searchPath
     *
     * @return string
     */
    private function buildRequestTarget(RequestInterface $request, string $searchPath): string
    {
        $query = $request->getUri()->getQuery();

        if ($query !== '') {
            $searchPath .= '?' . $request->getUri();
        }

        return $searchPath;
    }
}
