<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Helpers;

use Doctrine\Common\Persistence\ManagerRegistry;
use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use EoneoPay\Externals\Bridge\Laravel\Providers\ContainerServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Providers\HttpClientServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Providers\ValidationConstraintServiceProvider;
use EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\LoggingClient;
use EoneoPay\Utils\AnnotationReader;
use EoneoPay\Utils\Arr;
use EoneoPay\Utils\Bridge\Lumen\Interfaces\Resolvers\ControllerResolverInterface;
use EoneoPay\Utils\Bridge\Lumen\Resolvers\ControllerResolver;
use EoneoPay\Utils\Generator;
use EoneoPay\Utils\Interfaces\AnnotationReaderInterface;
use EoneoPay\Utils\Interfaces\ArrInterface;
use EoneoPay\Utils\Interfaces\GeneratorInterface;
use EoneoPay\Utils\Interfaces\MathInterface;
use EoneoPay\Utils\Math;
use Illuminate\Contracts\Container\Container;
use Laravel\Lumen\Application;
use LoyaltyCorp\RequestHandlers\Bridge\Laravel\Providers\ParamConverterProvider;
use LoyaltyCorp\RequestHandlers\Middleware\ParamConverterMiddleware;
use LoyaltyCorp\RequestHandlers\Middleware\ValidatingMiddleware;
use LoyaltyCorp\RequestHandlers\Serializer\DoctrineDenormalizer;

final class ApplicationInstantiator
{
    /**
     * Creates a new instance of the Application for testing purposes.
     *
     * @return \Laravel\Lumen\Application
     */
    public static function create(): Application
    {
        $basePath = \realpath(\dirname(__DIR__, 2));
        require_once $basePath . '/vendor/autoload.php';

        $app = new Application($basePath);

        $app->singleton(DoctrineDenormalizer::class, static function (Container $app): DoctrineDenormalizer {
            return new DoctrineDenormalizer(
                $app->make(ManagerRegistry::class)
            );
        });

        $app->configure('database');

        // Service providers required by the framework
        $app->register(ContainerServiceProvider::class);
        $app->register(ParamConverterProvider::class);

        // Service providers for the application
        $app->register(HttpClientServiceProvider::class);
        $app->register(ValidationConstraintServiceProvider::class);

        $app->singleton(AnnotationReaderInterface::class, AnnotationReader::class);
        $app->singleton(ArrInterface::class, Arr::class);
        $app->singleton(ClientInterface::class, LoggingClient::class);
        $app->singleton(ControllerResolverInterface::class, ControllerResolver::class);
        $app->singleton(EventDispatcherInterface::class, EventDispatcher::class);
        $app->singleton(GeneratorInterface::class, Generator::class);
        $app->singleton(MathInterface::class, Math::class);

        $app->routeMiddleware([
            'param_converter' => ParamConverterMiddleware::class,
            'request_validator' => ValidatingMiddleware::class
        ]);

        return $app;
    }
}
