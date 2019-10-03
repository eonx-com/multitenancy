<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Bridge\Laravel\Providers;

use LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\RequestHandlersServiceProvider;
use LoyaltyCorp\Multitenancy\Services\Requests\Interfaces\ProviderAwareObjectBuilderInterface;
use LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareEntityFinder;
use LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareObjectBuilder;
use LoyaltyCorp\Multitenancy\Services\Requests\RequestBodyContextConfigurator;
use LoyaltyCorp\RequestHandlers\Request\Interfaces\ContextConfiguratorInterface;
use LoyaltyCorp\RequestHandlers\Serializer\Interfaces\DoctrineDenormalizerEntityFinderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Symfony\SerializerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ServiceProviderTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\RequestHandlersServiceProvider
 */
final class RequestHandlersServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getBindings(): array
    {
        return [
            ContextConfiguratorInterface::class => RequestBodyContextConfigurator::class,
            DoctrineDenormalizerEntityFinderInterface::class => ProviderAwareEntityFinder::class,
            ProviderAwareObjectBuilderInterface::class => ProviderAwareObjectBuilder::class,
        ];
    }

    /**
     * Overridden to bind required services.
     *
     * {@inheritdoc}
     */
    public function testBindings(): void
    {
        $this->app->bind(SerializerInterface::class, SerializerStub::class);

        parent::testBindings();
    }

    /**
     * {@inheritdoc}
     */
    protected function getServiceProvider(): string
    {
        return RequestHandlersServiceProvider::class;
    }
}
