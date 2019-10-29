<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Search;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Search\ProviderAwareRequestProxyFactory;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;
use Zend\Diactoros\ServerRequest;
use function GuzzleHttp\Psr7\stream_for;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Search\ProviderAwareRequestProxyFactory
 */
final class ProviderAwareRequestProxyFactoryTest extends AppTestCase
{
    /**
     * Tests the create createProxyRequest method when the configured elasticsearch
     * host contains a username and password.
     *
     * @return void
     */
    public function testCreateProxyRequestWithAuthentication(): void
    {
        $provider = new Provider('provider_id', 'name');
        $request = new ServerRequest(
            [],
            [],
            'https://subscriptions.system.example/search/index/_doc/_search?pp=5',
            'POST',
            stream_for('request body')
        );
        $request = $request->withAttribute('_encoder', 'value');

        $expectedUri = 'https://admin:password@127.0.0.3:9200/index_provider_id/_doc/_search?pp=5';
        $expectedAuth = 'Basic ' . \base64_encode('admin:password');

        $instance = $this->getInstance();

        $result = $instance->createProxyRequest($provider, $request);

        self::assertSame($expectedUri, (string)$result->getUri());
        self::assertSame($expectedAuth, $result->getHeaderLine('Authorization'));
        self::assertSame('request body', (string)$result->getBody());
        self::assertInstanceOf(ServerRequest::class, $result);
        /** @var \Zend\Diactoros\ServerRequest $result */
        self::assertNull($result->getAttribute('_encoder'));
    }

    /**
     * Tests the create createProxyRequest method when the configured elasticsearch
     * host has no authentication details.
     *
     * @return void
     */
    public function testCreateProxyRequestWithoutAuthentication(): void
    {
        $provider = new Provider('provider_id', 'name');
        $request = new ServerRequest(
            [],
            [],
            'https://subscriptions.system.example/search/index/_doc/_search?pp=5',
            'POST',
            stream_for('request body')
        );
        $request = $request->withAttribute('_encoder', 'value');

        $expectedUri = 'https://127.0.0.4/index_provider_id/_doc/_search?pp=5';

        $instance = $this->getInstance('https://127.0.0.4');

        $result = $instance->createProxyRequest($provider, $request);

        self::assertSame($expectedUri, (string)$result->getUri());
        self::assertSame('', $result->getHeaderLine('Authorization'));
        self::assertSame('request body', (string)$result->getBody());
        self::assertInstanceOf(ServerRequest::class, $result);
        /** @var \Zend\Diactoros\ServerRequest $result */
        self::assertNull($result->getAttribute('_encoder'));
    }

    /**
     * Tests the create createProxyRequest method when no index provided.
     *
     * @return void
     */
    public function testCreateProxyRequestWithoutIndex(): void
    {
        $provider = new Provider('id', 'name');
        $request = new ServerRequest(
            [],
            [],
            'https://subscriptions.system.example/search',
            'POST',
            stream_for('request body')
        );
        $request = $request->withAttribute('_encoder', 'value');

        $expectedUri = 'https://127.0.0.4';

        $instance = $this->getInstance('https://127.0.0.4');

        $result = $instance->createProxyRequest($provider, $request);

        self::assertSame($expectedUri, (string)$result->getUri());
        self::assertSame('', $result->getHeaderLine('Authorization'));
        self::assertSame('request body', (string)$result->getBody());
        self::assertInstanceOf(ServerRequest::class, $result);
        /** @var \Zend\Diactoros\ServerRequest $result */
        self::assertNull($result->getAttribute('_encoder'));
    }

    /**
     * Returns the instance under test.
     *
     * @param string|null $elasticHost
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Search\ProviderAwareRequestProxyFactory
     */
    private function getInstance(?string $elasticHost = null): ProviderAwareRequestProxyFactory
    {
        return new ProviderAwareRequestProxyFactory($elasticHost ?? 'https://admin:password@127.0.0.3:9200');
    }
}
