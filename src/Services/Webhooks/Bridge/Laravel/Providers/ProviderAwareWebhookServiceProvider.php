<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Laravel\Providers;

use EoneoPay\Webhooks\Bridge\Laravel\Providers\WebhookServiceProvider;
use Illuminate\Support\ServiceProvider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\Interfaces\ProviderAwareActivityFactoryInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\ProviderAwareActivityFactory;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\ActivityHandler;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Persister\ActivityPersister;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface;

final class ProviderAwareWebhookServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        // Ensure webhooks service provider is bound
        $this->app->register(WebhookServiceProvider::class);

        $this->app->singleton(ActivityHandlerInterface::class, ActivityHandler::class);
        $this->app->singleton(ActivityPersisterInterface::class, ActivityPersister::class);
        $this->app->singleton(ProviderAwareActivityFactoryInterface::class, ProviderAwareActivityFactory::class);
    }
}
