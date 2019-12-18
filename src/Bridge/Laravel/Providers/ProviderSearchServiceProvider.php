<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use LoyaltyCorp\Multitenancy\Services\Search\Interfaces\ProviderAwareRequestProxyFactoryInterface;
use LoyaltyCorp\Multitenancy\Services\Search\ProviderAwareRequestProxyFactory;
use LoyaltyCorp\Multitenancy\Services\Search\Transformers\ProviderIndexTransformer;
use LoyaltyCorp\Search\Interfaces\Transformers\IndexNameTransformerInterface;

final class ProviderSearchServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton(IndexNameTransformerInterface::class, ProviderIndexTransformer::class);

        // Bind proxy factory
        $this->app->singleton(
            ProviderAwareRequestProxyFactoryInterface::class,
            static function (): ProviderAwareRequestProxyFactory {
                return new ProviderAwareRequestProxyFactory(
                    (string)\env('ELASTICSEARCH_HOST', 'https://admin:admin@elasticsearch:9200')
                );
            }
        );
    }
}
