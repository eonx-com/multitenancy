<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Database\Entities;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException;
use Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\Integration\TestCases\HasProviderTestCase;

/**
 * This test class tests an entity stub which is using HasProvider trait.
 *
 * @covers \Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasProviderStub
 */
class HasProviderTest extends HasProviderTestCase
{
    /**
     * Test adding a provider to entity saves state to db.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException If provider isn't set prior to call
     */
    public function testHasProvider(): void
    {
        $entityManager = $this->getEntityManager();

        $provider = new Provider('99999991111111aaaaabbbbccccc', 'Acme Corp');
        $entityManager->persist($provider);

        $entity = new EntityHasProviderStub('ENTITY_ID', 'Test');
        $entity->setProvider($provider);
        $entityManager->persist($entity);
        $entityManager->flush();

        $this->getEntityManager()->clear(EntityHasProviderStub::class);

        // Test details were set on entity
        self::assertSame($provider, $entity->getProvider());
        self::assertSame($provider->getProviderId(), $entity->getProviderId());

        $repository = $entityManager->getRepository(EntityHasProviderStub::class);
        /**
         * @var \Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasProviderStub $actual
         */
        $actual = $repository->findOneBy(['externalId' => 'ENTITY_ID']);

        self::assertSame($provider, $actual->getProvider());
    }

    /**
     * Test has provider can accept null provider.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException If provider isn't set prior to call
     */
    public function testHasProviderThrowsExceptionIfProviderNotSet(): void
    {
        $entityManager = $this->getEntityManager();

        $entity = new EntityHasProviderStub('ENTITY_ID', 'Test');
        $entityManager->persist($entity);
        $entityManager->flush();

        $this->getEntityManager()->clear(EntityHasProviderStub::class);
        $repository = $entityManager->getRepository(EntityHasProviderStub::class);
        /**
         * @var \Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasProviderStub $actual
         */
        $actual = $repository->findOneBy(['externalId' => 'ENTITY_ID']);

        $this->expectException(ProviderNotSetException::class);

        $actual->getProvider();
    }
}
