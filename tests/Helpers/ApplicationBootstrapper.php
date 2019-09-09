<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Helpers;

use Doctrine\Common\Annotations\AnnotationReader as BaseAnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use EoneoPay\Externals\Bridge\Laravel\Providers\ContainerServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Providers\HttpClientServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Providers\RequestServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Providers\ValidationConstraintServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Providers\ValidationServiceProvider;
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
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use LoyaltyCorp\RequestHandlers\Bridge\Laravel\Providers\ParamConverterProvider;
use LoyaltyCorp\RequestHandlers\Middleware\ParamConverterMiddleware;
use LoyaltyCorp\RequestHandlers\Middleware\ValidatingMiddleware;
use LoyaltyCorp\RequestHandlers\Serializer\DoctrineDenormalizer;
use LoyaltyCorp\RequestHandlers\Serializer\Interfaces\DoctrineDenormalizerEntityFinderInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\Common\Persistence\ManagerRegistryStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\RequestHandlers\Serializer\EntityFinderStub;

/**
 * This class bootstraps an application for use in testing.
 *
 * @coversNothing
 *
 * @internal Only for use with tests within this library.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Ignore coupling as the bootstrapper is used for testing.
 * @SuppressWarnings(PHPMD.StaticAccess) Static access to some classes required for testing
 */
final class ApplicationBootstrapper
{
    /**
     * Creates a new instance of the Application for testing purposes.
     *
     * @return \Laravel\Lumen\Application
     */
    public static function create(): Application
    {
        // Set the base path and include the autoloader
        $basePath = \dirname(__DIR__);
        require_once $basePath . '/../vendor/autoload.php';

        // Until Doctrine Annotations v2.0, we need to register an autoloader, which is just 'class_exists'.
        /** @noinspection PhpDeprecationInspection Will be removed with doctrine annotations v2.0 */
        AnnotationRegistry::registerUniqueLoader('class_exists');

        // Ignore @covers and @coversNothing annotations
        BaseAnnotationReader::addGlobalIgnoredName('covers');
        BaseAnnotationReader::addGlobalIgnoredName('coversNothing');

        // Create a new application
        $app = new Application($basePath);

        // Service providers required by the framework
        $app->register(ContainerServiceProvider::class);
        $app->register(ParamConverterProvider::class);
        $app->register(RequestServiceProvider::class);
        $app->register(DoctrineServiceProvider::class);

        // Service providers for the application
        $app->register(HttpClientServiceProvider::class);
        $app->register(ValidationServiceProvider::class);
        $app->register(ValidationConstraintServiceProvider::class);

        // Register any singletons
        $app->singleton(AnnotationReaderInterface::class, AnnotationReader::class);
        $app->singleton(ArrInterface::class, Arr::class);
        $app->singleton(ClientInterface::class, LoggingClient::class);
        $app->singleton(ControllerResolverInterface::class, ControllerResolver::class);
        $app->singleton(DoctrineDenormalizerEntityFinderInterface::class, EntityFinderStub::class);
        $app->singleton(EventDispatcherInterface::class, EventDispatcher::class);
        $app->singleton(GeneratorInterface::class, Generator::class);
        $app->singleton(MathInterface::class, Math::class);
        $app->singleton(DoctrineDenormalizer::class, static function (Container $app): DoctrineDenormalizer {
            return new DoctrineDenormalizer(
                new EntityFinderStub(),
                $app->make(ManagerRegistry::class)
            );
        });

        // Register any static instances
        $app->instance(ManagerRegistry::class, new ManagerRegistryStub());

        // Register route middlewares
        $app->routeMiddleware([
            'param_converter' => ParamConverterMiddleware::class,
            'request_validator' => ValidatingMiddleware::class,
        ]);

        return $app;
    }
}
