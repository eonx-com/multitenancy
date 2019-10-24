<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Bridge\Laravel\Providers;

use EoneoPay\Externals\ORM\EntityManager;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Container\Container;
use LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\ProviderSearchServiceProvider;
use LoyaltyCorp\Multitenancy\Services\Search\Transformers\ProviderIndexTransformer;
use LoyaltyCorp\Search\Interfaces\Transformers\IndexNameTransformerInterface;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ServiceProviderTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\ProviderSearchServiceProvider
 */
final class ProviderSearchServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     *
     * Set up is used in this test to bind externals entity manager which is injected
     * in ProviderIndexTransformer.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->bindExternalsEM();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBindings(): array
    {
        return [
            IndexNameTransformerInterface::class => ProviderIndexTransformer::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getServiceProvider(): string
    {
        return ProviderSearchServiceProvider::class;
    }

    /**
     * Bind externals entity manager.
     *
     * @return void
     */
    private function bindExternalsEM(): void
    {
        $this->app->bind(EntityManagerInterface::class, static function (Container $app): EntityManager {
            $entityManager = $app->make('registry')->getManager();

            return new EntityManager($entityManager);
        });
    }
}
