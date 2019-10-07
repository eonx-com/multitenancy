<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Webhooks\Bridge\Laravel\Providers;

use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface as ExternalEntityManagerInterface;
use Illuminate\Container\Container;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\Interfaces\ProviderAwareActivityFactoryInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\ProviderAwareActivityFactory;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\ActivityHandler;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Persister\ActivityPersister;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Laravel\Providers\ProviderAwareWebhookServiceProvider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Persister\Interfaces\ActivityPersisterInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\EntityManagerStub as ExternalEntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Laravel\Providers\ProviderAwareWebhookServiceProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Test case only, high coupling required to fully test service provider
 */
final class ProviderAwareWebhookServiceProviderTest extends AppTestCase
{
    /**
     * @var string[]
     */
    private $bindings = [
        ActivityHandlerInterface::class => ActivityHandler::class,
        ActivityPersisterInterface::class => ActivityPersister::class,
        ProviderAwareActivityFactoryInterface::class => ProviderAwareActivityFactory::class,
    ];

    /**
     * Test provider register container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $app = $this->registerServiceProvider();

        foreach ($this->bindings as $interface => $concrete) {
            self::assertInstanceOf($concrete, $app->get($interface));
        }
    }

    /**
     * Register service provider.
     *
     * @return \Illuminate\Container\Container
     */
    private function registerServiceProvider(): Container
    {
        $app = clone $this->app;

        // Bind required services from other service providers
        $app->bind(LoggerInterface::class, Logger::class);
        $app->bind(ExternalEntityManagerInterface::class, ExternalEntityManagerStub::class);
        $app->bind(EntityManagerInterface::class, EntityManagerStub::class);

        /** @noinspection PhpParamsInspection Lumen application is a foundation application */
        $provider = new ProviderAwareWebhookServiceProvider($app);
        $provider->register();

        return $app;
    }
}
