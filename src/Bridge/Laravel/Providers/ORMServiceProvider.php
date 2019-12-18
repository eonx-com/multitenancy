<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;

final class ORMServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton(EntityManagerInterface::class, static function (Container $app): EntityManager {
            $entityManager = $app->make('registry')->getManager();

            return new EntityManager($entityManager);
        });
    }
}
