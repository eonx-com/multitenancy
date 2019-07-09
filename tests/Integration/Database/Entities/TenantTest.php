<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Database\Entities;

use LoyaltyCorp\Mulitenancy\Database\Entities\Tenant;
use Tests\LoyaltyCorp\Multitenancy\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Mulitenancy\Database\Entities\Tenant
 */
class TenantTest extends DoctrineTestCase
{
    /**
     * Test that Tenants can be successfully persisted to the database.
     *
     * @return void
     */
    public function testPersistence(): void
    {
        $entity = new Tenant('99999991111111aaaaabbbbccccc', 'Acme Corp');
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear(Tenant::class);
        $repository = $this->getEntityManager()->getRepository(Tenant::class);

        $actual = $repository->findOneBy(['externalId' => '99999991111111aaaaabbbbccccc']);

        $this->assertEquals($entity, $actual);
    }
}
