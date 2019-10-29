<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Controllers;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Http\Controllers\SearchController;
use LoyaltyCorp\Multitenancy\Services\Search\ProviderAwareRequestProxyFactory;
use Psr\Http\Message\ResponseInterface;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use function GuzzleHttp\Psr7\str;
use function GuzzleHttp\Psr7\stream_for;

/**
 * @covers \LoyaltyCorp\Multitenancy\Http\Controllers\SearchController
 */
final class SearchControllerTest extends AppTestCase
{
    /**
     * Tests the search action.
     *
     * @return void
     */
    public function testSearch(): void
    {
        $provider = new Provider('id', 'name');
        $request = new ServerRequest(
            [],
            [],
            'https://search.example/search/customers/_search',
            'POST',
        );

        $response = new Response();
        $controller = $this->getInstance($response);

        $result = $controller->search($provider, $request);

        self::assertSame(200, $result->getStatusCode());
        self::assertSame(str($response), str($result));
    }

    /**
     * Tests the search action removes CORS headers from the response.
     *
     * @return void
     */
    public function testSearchStripsCors(): void
    {
        $provider = new Provider('id', 'name');
        $request = new ServerRequest(
            [],
            [],
            'https://subscriptions.system.example/search/index/_doc/_search?pp=5',
            'POST',
            stream_for(),
        );

        $response = new Response(stream_for(), 200, [
            'Access-Control-Allow-Origin' => 'Yep',
            'Access-Control-Allow-Credentials' => 'Yep',
            'Access-Control-Allow-Headers' => 'Yep',
            'Access-Control-Allow-Methods' => 'Yep',
            'Access-Control-Expose-Headers' => 'Yep',
            'Access-Control-Max-Age' => 'Yep',
        ]);

        $expectedResponse = new Response();
        $controller = $this->getInstance($response);

        $result = $controller->search($provider, $request);

        self::assertSame(200, $result->getStatusCode());
        self::assertSame(str($expectedResponse), str($result));
    }

    /**
     * Returns the controller under test.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \LoyaltyCorp\Multitenancy\Http\Controllers\SearchController
     */
    private function getInstance(ResponseInterface $response): SearchController
    {
        return new SearchController(
            new Client(
                new GuzzleClient(['handler' => new MockHandler([$response])]),
                new ExceptionHandler()
            ),
            new ProviderAwareRequestProxyFactory('https://localhost:9200')
        );
    }
}
