<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Middleware;

use Closure;
use EoneoPay\Externals\Auth\Interfaces\AuthInterface;
use Illuminate\Http\Request;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
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

        // If user isn't something with a provider, skip
        if (($user instanceof HasProviderInterface) === false) {
            return $next($request);
        }

        // Get provider from resolver
        /**
         * @var \LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface $user
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
         */
        $provider = $this->providerResolver->resolve($user);

        // add provider to lumen route
        $route[2] = \array_merge($route[2] ?? [], [
            'provider' => $provider,
        ]);

        // add provider to symfony request
        $request->attributes->set('provider', $provider);

        $request->setRouteResolver(static function () use ($route) {
            return $route;
        });

        return $next($request);
    }
}
