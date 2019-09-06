<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Database\Entities;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * This test class tests an entity stub which is using HasProvider trait.
 *
 * @coversNothing
 */
final class HasProviderTest extends DoctrineTestCase
{
    /**
     * Test adding a provider to entity saves state to db.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
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
        $repository = $entityManager->getRepository(EntityHasProviderStub::class);
        /**
         * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub $actual
         */
        $actual = $repository->findOneBy(['externalId' => 'ENTITY_ID']);

        self::assertSame($provider, $actual->getProvider());
    }

    /**
     * Test has provider can accept null provider.
     *
     * @return void
     */
    public function testHasProviderCanSaveNullProvider(): void
    {
        $entityManager = $this->getEntityManager();

        $entity = new EntityHasProviderStub('ENTITY_ID', 'Test');
        $entityManager->persist($entity);
        $entityManager->flush();

        $this->getEntityManager()->clear(EntityHasProviderStub::class);
        $repository = $entityManager->getRepository(EntityHasProviderStub::class);
        /**
         * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub $actual
         */
        $actual = $repository->findOneBy(['externalId' => 'ENTITY_ID']);

        self::assertNull($actual->getProvider());
    }

    /**
     * Test fluency.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     */
    public function testHasProviderIsFluent(): void
    {
        $entity = new EntityHasProviderStub('ENTITY_ID', 'Test');

        $provider = new Provider('99999991111111aaaaabbbbccccc', 'Acme Corp');

        self::assertSame($entity, $entity->setProvider($provider));
    }
}
