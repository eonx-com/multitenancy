<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use LoyaltyCorp\Multitenancy\Services\Requests\Interfaces\ProviderAwareObjectBuilderInterface;
use LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareEntityFinder;
use LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareObjectBuilder;
use LoyaltyCorp\Multitenancy\Services\Requests\RequestBodyContextConfigurator;
use LoyaltyCorp\RequestHandlers\Request\Interfaces\ContextConfiguratorInterface;
use LoyaltyCorp\RequestHandlers\Serializer\Interfaces\DoctrineDenormalizerEntityFinderInterface;

final class RequestHandlersServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bind(
            ContextConfiguratorInterface::class,
            RequestBodyContextConfigurator::class
        );
        $this->app->bind(
            DoctrineDenormalizerEntityFinderInterface::class,
            ProviderAwareEntityFinder::class
        );
        $this->app->bind(
            ProviderAwareObjectBuilderInterface::class,
            ProviderAwareObjectBuilder::class
        );
    }
}
