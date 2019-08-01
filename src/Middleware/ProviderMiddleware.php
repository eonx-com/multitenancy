<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Middleware;

use Closure;
use EoneoPay\Externals\Auth\Interfaces\AuthInterface;
use Illuminate\Http\Request;
use LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces\ProviderResolverInterface;

final class ProviderMiddleware
{
    /**
     * @var \EoneoPay\Externals\Auth\Interfaces\AuthInterface
     */
    private $auth;

    /**
     * @var \LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces\ProviderResolverInterface
     */
    private $providerResolver;

    /**
     * ProviderMiddleware constructor.
     *
     * @param \EoneoPay\Externals\Auth\Interfaces\AuthInterface $auth
     * @param \LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces\ProviderResolverInterface $providerResolver
     */
    public function __construct(AuthInterface $auth, ProviderResolverInterface $providerResolver)
    {
        $this->auth = $auth;
        $this->providerResolver = $providerResolver;
    }

    /**
     * Provider middleware handler.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        if (\is_array($route) === false) {
            return $next($request);
        }

        $user = $this->auth->user();

        // return and let others handle this.
        if ($user === null) {
            return $next($request);
        }

        // get provider from resolver
        $provider = $this->providerResolver->getProvider($user);

        // add provider to symfony request
        $request->attributes->add(
            \array_merge($route[2] ?? [], [
                'provider' => $provider
            ])
        );

        // add provider to lumen route
        // Put the Symfony request attributes back into the laravel route.
        foreach ($request->attributes as $key => $attribute) {
            /** @noinspection UnsupportedStringOffsetOperationsInspection */
            $route[2][$key] = $attribute;
        }

        $request->setRouteResolver(static function () use ($route) {
            return $route;
        });

        return $next($request);
    }
}
