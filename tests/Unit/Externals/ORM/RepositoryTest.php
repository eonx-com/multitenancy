<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException;
use ReflectionClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasCompositePrimaryKeyStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityImplementsRepositoryInterfaceStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\RepositoryStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\Repository
 */
final class RepositoryTest extends DoctrineTestCase
{
    /**
     * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityImplementsRepositoryInterfaceStub
     */
    private $entity1;

    /**
     * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityImplementsRepositoryInterfaceStub
     */
    private $entity2;

    /**
     * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityImplementsRepositoryInterfaceStub
     */
    private $entity3;

    /**
     * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityImplementsRepositoryInterfaceStub
     */
    private $entity4;

    /**
     * Test count works with criteria and provider.
     *
     * @return void
     */
    public function testCountFunctionality(): void
    {
        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create several entities
        $this->createEntities($provider1, $provider2);

        $instance = $this->createInstance();

        // Test count provider 1
        $count1 = $instance->count($provider1);
        self::assertSame(3, $count1);

        // Test count provider 2
        $count2 = $instance->count($provider2);
        self::assertSame(1, $count2);

        // Test count with criteria
        $countCriteria1 = $instance->count($provider1, ['string' => 'one']);
        self::assertSame(2, $countCriteria1);

        // Test count with criteria
        $countCriteria2 = $instance->count($provider1, ['string' => 'two']);
        self::assertSame(1, $countCriteria2);

        // Test count with criteria with wrong provider
        $countCriteria3 = $instance->count($provider2, ['string' => 'two']);
        self::assertSame(0, $countCriteria3);
    }

    /**
     * Test create query builder.
     *
     * @return void
     *
     * @throws \ReflectionException If reflection does something whacky
     */
    public function testCreateQueryBuilderFunctionality(): void
    {
        $instance = $this->createInstance();

        // Reflect so we can create a query builder
        $reflected = new ReflectionClass(RepositoryStub::class);
        $method = $reflected->getMethod('createQueryBuilder');
        $method->setAccessible(true);

        $result = $method->invoke($instance, 'test');

        self::assertSame(['test'], $result->getAllAliases());
    }

    /**
     * Test exceptions thrown by doctrine are converted by the repository.
     *
     * @return void
     */
    public function testExceptionsFromDoctrineAreConverted(): void
    {
        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create several entities
        $this->createEntities($provider1, $provider2);

        $instance = $this->createInstance();

        $this->expectException(ORMException::class);

        // Using an invalid property should throw an exception
        $instance->count($provider1, ['invalidProperty' => 1]);
    }

    /**
     * Test find all works with criteria and provider.
     *
     * @return void
     */
    public function testFindAllFunctionality(): void
    {
        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create several entities
        $this->createEntities($provider1, $provider2);

        $instance = $this->createInstance();

        // Test finding all entities for provider 1
        $found1 = $instance->findAll($provider1);
        self::assertSame([$this->entity1, $this->entity2, $this->entity3], $found1);

        // Test finding all entities for provider 2
        $found2 = $instance->findAll($provider2);
        self::assertSame([$this->entity4], $found2);
    }

    /**
     * Test find by works with criteria and provider.
     *
     * @return void
     */
    public function testFindByFunctionality(): void
    {
        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create several entities
        $this->createEntities($provider1, $provider2);

        $instance = $this->createInstance();

        // Test finding entities for provider 1
        $found1 = $instance->findBy($provider1, []);
        self::assertSame([$this->entity1, $this->entity2, $this->entity3], $found1);

        // Test finding entities for provider 2
        $found2 = $instance->findBy($provider2, []);
        self::assertSame([$this->entity4], $found2);

        // Test finding entities with criteria
        $found3 = $instance->findBy($provider1, ['string' => 'one']);
        self::assertSame([$this->entity1, $this->entity2], $found3);

        // Test finding entities with criteria
        $found4 = $instance->findBy($provider1, ['string' => 'two']);
        self::assertSame([$this->entity3], $found4);

        // Test finding entities with the wrong provider
        $found5 = $instance->findBy($provider2, ['string' => 'two']);
        self::assertSame([], $found5);
    }

    /**
     * Test find works with criteria and provider.
     *
     * @return void
     */
    public function testFindFunctionality(): void
    {
        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create several entities
        $this->createEntities($provider1, $provider2);

        $instance = $this->createInstance();

        // Test finding an entity
        $found1 = $instance->find($provider1, $this->entity1->getEntityId());
        self::assertSame($this->entity1, $found1);

        // Test provider mismatch
        $found2 = $instance->find($provider2, $this->entity1->getEntityId());
        self::assertNull($found2);
    }

    /**
     * Test find one by works with criteria and provider.
     *
     * @return void
     */
    public function testFindOneByFunctionality(): void
    {
        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create several entities
        $this->createEntities($provider1, $provider2);

        $instance = $this->createInstance();

        // Test finding an entity for provider 1
        $found1 = $instance->findOneBy($provider1, []);
        self::assertSame($this->entity1, $found1);

        // Test finding an entity for provider 2
        $found2 = $instance->findOneBy($provider2, []);
        self::assertSame($this->entity4, $found2);

        // Test finding an entity with criteria
        $found3 = $instance->findOneBy($provider1, ['string' => 'one']);
        self::assertSame($this->entity1, $found3);

        // Test finding an entity with criteria
        $found4 = $instance->findOneBy($provider1, ['string' => 'two']);
        self::assertSame($this->entity3, $found4);

        // Test finding an entity with the wrong provider
        $found5 = $instance->findOneBy($provider2, ['string' => 'two']);
        self::assertNull($found5);
    }

    /**
     * Test find works with criteria and provider.
     *
     * @return void
     */
    public function testFindThrowsExceptionIfEntityUsesCompositeKeys(): void
    {
        // Create provider
        $provider = $this->createProvider('provider');

        $instance = $this->createInstance(EntityHasCompositePrimaryKeyStub::class);

        $this->expectException(ORMException::class);

        // Find should throw exception
        $instance->find($provider, 1);
    }

    /**
     * Test get class name functionality.
     *
     * @return void
     */
    public function testGetClassNameReturnsEntityClass(): void
    {
        $instance = $this->createInstance(EntityHasCompositePrimaryKeyStub::class);

        $name = $instance->getClassName();
        self::assertSame(EntityHasCompositePrimaryKeyStub::class, $name);
    }

    /**
     * Create repository instance.
     *
     * @param string|null $class Class to instantiate repository for
     *
     * @return \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface
     */
    protected function createInstance(?string $class = null): RepositoryInterface
    {
        $entityManager = new EntityManager($this->getEntityManager());

        try {
            return $entityManager->getRepository($class ?? EntityImplementsRepositoryInterfaceStub::class);
        } catch (RepositoryDoesNotImplementInterfaceException $exception) {
            self::fail(\sprintf('Exception thrown when creating repository: %s', $exception->getMessage()));
        }

        return new RepositoryStub($this->getEntityManager(), new ClassMetadata(''));
    }

    /**
     * Create entities for two providers.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider1
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider2
     *
     * @return void
     */
    private function createEntities(Provider $provider1, Provider $provider2): void
    {
        $this->entity1 = new EntityImplementsRepositoryInterfaceStub('one');
        $this->entity2 = new EntityImplementsRepositoryInterfaceStub('one');
        $this->entity3 = new EntityImplementsRepositoryInterfaceStub('two');
        $this->entity4 = new EntityImplementsRepositoryInterfaceStub('one');

        $entityManager = new EntityManager($this->getEntityManager());

        $entityManager->persist($provider1, $this->entity1);
        $entityManager->persist($provider1, $this->entity2);
        $entityManager->persist($provider1, $this->entity3);
        $entityManager->flush($provider1);

        $entityManager->persist($provider2, $this->entity4);
        $entityManager->flush($provider2);
    }
}
