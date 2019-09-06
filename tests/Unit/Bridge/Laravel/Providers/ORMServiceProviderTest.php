<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Bridge\Laravel\Providers;

use LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\ORMServiceProvider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ServiceProviderTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\ORMServiceProvider
 */
final class ORMServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getBindings(): array
    {
        return [
            EntityManagerInterface::class => EntityManager::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getServiceProvider(): string
    {
        return ORMServiceProvider::class;
    }
}
