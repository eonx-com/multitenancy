<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM\Query;

use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\Query\FilterStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\Query\FilterCollection
 */
final class FilterCollectionTest extends DoctrineTestCase
{
    /**
     * Test filters collection methods enable/disable filters on entity manager.
     *
     * @return void
     */
    public function testFiltersCollectionMethodsSuccessful(): void
    {
        // Create doctrine instance and get filters
        $doctrine = $this->getEntityManager();
        $doctrineFilters = $doctrine->getFilters();

        // Add filter we can disable
        $config = $doctrine->getConfiguration();
        $config->addFilter('test-filter', FilterStub::class);

        // Create entity manager instance and get filters
        $entityManager = new EntityManager($doctrine);
        $filters = $entityManager->getFilters();

        // Test no filters are enabled
        $enabled = $doctrineFilters->getEnabledFilters();
        self::assertSame([], $enabled);

        // Enable and test
        $filters->enable('test-filter');
        $enabled = $doctrineFilters->getEnabledFilters();
        self::assertNotEmpty($enabled);
        self::assertSame(['test-filter'], \array_keys($enabled));

        // Disable and test
        $filters->disable('test-filter');
        $enabled = $doctrineFilters->getEnabledFilters();
        self::assertSame([], $enabled);
    }

    /**
     * Test an invalid filter throws an exception.
     *
     * @return void
     */
    public function testInvalidFilterThrowsException(): void
    {
        $entityManager = new EntityManager($this->getEntityManager());
        $filters = $entityManager->getFilters();

        $this->expectException(ORMException::class);

        $filters->enable('invalid');
    }
}
