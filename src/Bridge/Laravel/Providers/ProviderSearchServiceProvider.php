<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
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
        $this->app->bind(IndexNameTransformerInterface::class, ProviderIndexTransformer::class);
    }
}
