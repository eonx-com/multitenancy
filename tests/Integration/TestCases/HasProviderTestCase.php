<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\TestCases;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @coversNothing
 */
class HasProviderTestCase extends DoctrineTestCase
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Static access to entity manager required to create instance
     */
    protected function getDoctrineEntityManager(): EntityManagerInterface
    {
        $paths = [__DIR__ . '/../../../src', __DIR__ . '/../Stubs/Database'];
        $setup = new Setup();
        $config = $setup::createAnnotationMetadataConfiguration($paths, true, null, null, false);
        $dbParams = ['driver' => 'pdo_sqlite', 'memory' => true];

        return EntityManager::create($dbParams, $config);
    }
}
