<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Middleware;

use EoneoPay\Externals\Auth\Interfaces\AuthInterface;
use Illuminate\Http\Request;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Middleware\ProviderMiddleware;
use LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces\ProviderResolverInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\ProviderResolver\ProviderResolverStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\Auth\AuthStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\UserStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Middleware\ProviderMiddleware
 */
class ProviderMiddlewareTest extends AppTestCase
{
    /**
     * Test handle adds provider to route and attributes.
     *
     * @return void
     */
    public function testHandle(): void
    {
        $provider = new Provider('PROVIDER_ID', 'Loyalty Corp.');
        $providerResolver = new ProviderResolverStub($provider);
        $middleware = $this->getMiddleware(
            new AuthStub(new UserStub()),
            $providerResolver
        );

        $expectedRoute = [
            null,
            ['uses' => 'Class@method'],
            ['provider' => $provider]
        ];

        $request = new Request();
        $request->setRouteResolver(static function () {
            return [
                null,
                ['uses' => 'Class@method']
            ];
        });

        $next = static function () {
            return 'OK';
        };

        $result = $middleware->handle($request, $next);

        self::assertInstanceOf(Provider::class, $request->attributes->get('provider'));
        self::assertSame($provider, $request->attributes->get('provider'));
        self::assertSame($expectedRoute, $request->route());
        self::assertSame('OK', $result);
    }

    /**
     * Test when bad route is encountered the control is passed to next in line middleware.
     * Bad route is when the route is not of type array.
     *
     * @return void
     */
    public function testHandleBadRoute(): void
    {
        $request = new Request();
        $middleware = $this->getMiddleware();

        $next = static function () {
            return 'OK';
        };

        $result = $middleware->handle($request, $next);
        self::assertSame('OK', $result);
    }

    /**
     * Test handle passes control to next in line middleware when no auth user found.
     *
     * @return void
     */
    public function testHandleWhenNoAuthUser(): void
    {
        $request = new Request();
        $request->setRouteResolver(static function () {
            return [];
        });

        // this will return a null user.
        $auth = new AuthStub(null);
        $middleware = $this->getMiddleware($auth);

        $next = static function () {
            return 'OK';
        };

        $result = $middleware->handle($request, $next);
        self::assertSame('OK', $result);
    }

    /**
     * Test existing params in lumen route are kept.
     *
     * @return void
     */
    public function testHandleWhenRouteHasExistingParams(): void
    {
        $provider = new Provider('PROVIDER_ID', 'Loyalty Corp.');
        $providerResolver = new ProviderResolverStub($provider);
        $middleware = $this->getMiddleware(
            new AuthStub(new UserStub()),
            $providerResolver
        );

        $expectedRoute = [
            null,
            ['uses' => 'Class@method'],
            ['this' => 'that', 'provider' => $provider]
        ];

        $request = new Request();
        $request->setRouteResolver(static function () {
            return [
                null,
                ['uses' => 'Class@method'],
                ['this' => 'that']
            ];
        });

        $next = static function () {
            return 'OK';
        };

        $result = $middleware->handle($request, $next);

        self::assertInstanceOf(Provider::class, $request->attributes->get('provider'));
        self::assertSame($provider, $request->attributes->get('provider'));
        self::assertSame($expectedRoute, $request->route());
        self::assertSame('OK', $result);
    }

    /**
     * Get middleware instance
     *
     * @param \EoneoPay\Externals\Auth\Interfaces\AuthInterface|null $auth
     * @param \LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces\ProviderResolverInterface|null $providerResolver
     *
     * @return \LoyaltyCorp\Multitenancy\Middleware\ProviderMiddleware
     */
    private function getMiddleware(
        ?AuthInterface $auth = null,
        ?ProviderResolverInterface $providerResolver = null
    ): ProviderMiddleware {
        return new ProviderMiddleware(
            $auth ?? new AuthStub(null),
            $providerResolver ?? new ProviderResolverStub()
        );
    }
}
