<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Database\Entities;

use LoyaltyCorp\Mulitenancy\Database\Entities\Provider;
use Tests\LoyaltyCorp\Multitenancy\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Mulitenancy\Database\Entities\Provider
 */
class ProviderTest extends DoctrineTestCase
{
    /**
     * Test that Tenants can be successfully persisted to the database.
     *
     * @return void
     */
    public function testPersistence(): void
    {
        $entity = new Provider('99999991111111aaaaabbbbccccc', 'Acme Corp');
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear(Provider::class);
        $repository = $this->getEntityManager()->getRepository(Provider::class);

        $actual = $repository->findOneBy(['externalId' => '99999991111111aaaaabbbbccccc']);

        $this->assertEquals($entity, $actual);
    }
}
